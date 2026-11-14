<?php

namespace frontend\modules\checkout\eu\controllers;

use common\models\Order;
use common\models\ProductVariant;
use common\components\MailChimp;
use frontend\modules\checkout\eu\models\Checkout;
use frontend\modules\checkout\eu\models\delivery_methods\DeliveryMethod;
use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yz\shoppingcart\ShoppingCart;

/**
 * Cart Controller
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class CartController extends Controller
{
    /**
     * Cart page
     *
     * @return string
     */
    public function actionIndex()
    {
        /**
         * @var $cart ShoppingCart
         * @var ProductVariant $position
         */
        $session = Yii::$app->session;
        if ($session->has(Checkout::SESSION_KEY)) {
            $checkout = $session->get(Checkout::SESSION_KEY);
            if (isset($checkout['_delivery'])) {
                unset($checkout['_delivery']);
                $session->set(Checkout::SESSION_KEY, $checkout);
            }
        }
        if ($session->has(DeliveryMethod::SESSION_KEY)) {
            $session->remove(DeliveryMethod::SESSION_KEY);
        }
        return $this->render('cart');
    }

    /**
     * Checkout page
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionCheckout()
    {
        $model = new Checkout();

        if (\Yii::$app->cart->getCount() <= 0) {
            return $this->redirect('index');
        }

        if ($post = \Yii::$app->request->post()) {

            $model->load($post);
            $deliveryIsLoaded = $model->_delivery->load($post);
            $customerIsLoaded = $model->_customer->load($post);
            $paymentIsLoaded = $model->_payment->load($post);

            if ($customerIsLoaded && $deliveryIsLoaded && $paymentIsLoaded) {
                /** @var $deliveryMethod DeliveryMethod */;
                $deliveryMethod = $model->_delivery->method[$model->_delivery->delivery_method];

                if (!is_object($deliveryMethod)) {
                    $model->_delivery->addError('delivery_method', 'Необходимо выбрать "Способ доставки"');

                } else{

                    if ($deliveryMethod->load($post) && $model->composeOrder()) {

                        $order = Order::findOne($model->_order_id);

                        $order->sendOrderTo1c();
                        $order->sendEmail();
                        $order->sendGaTransaction();

                        return $this->redirect(Url::to(['/checkout/cart/checkout-complete', 'id' => $model->_order_id]));
                    }
                }
            }
        }

        return $this->render('checkout', ['model' => $model]);
    }

    /**
     * Complete page
     *
     * @param int $id app order id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCheckoutComplete($id)
    {
        if (!$order = Order::findOne($id)) {
            throw new NotFoundHttpException;
        }
        if (!\Yii::$app->session->has('complete_access_to_' . $id)) {
            if (\Yii::$app->user->isGuest || $order->customer_email !== \Yii::$app->user->getIdentity()->email) {
                throw new NotFoundHttpException;
            }
        }

        //(new MailChimp())->createOrder($order);

        if(!empty($_GET['payment'])){
            if($_GET['payment'] == 'success'){
                \frontend\widgets\Paytrail::confirmPayment();
            }
        }

        return $this->render('complete');
    }

    /**
     * Remove product from cart
     *
     * @param int $id product variant id
     * @throws BadRequestHttpException
     * @return string
     */
    public function actionRemove($id)
    {
        if (!$p = ProductVariant::findOne(['variant_id' => $id])) {
            throw new BadRequestHttpException();
        }
        \Yii::$app->cart->remove($p);
        return $this->redirect('index');
    }

    /**
     * Clear cart
     *
     * @return string
     */
    public function actionClear()
    {
        \Yii::$app->cart->removeAll();
        \Yii::$app->session->setFlash('info', 'Корзина очищена');
        return $this->redirect('index');
    }

    /**
     * Fill up cart from MailChimp
     *
     * @param string $uh base64 encoded user email
     * @return string
     */
    public function actionLoadMcCart($uh)
    {
        $r = MailChimp::fillCart($uh);
        return $r ? $this->redirect('index') : $this->goHome();
    }
}