<?php
namespace frontend\modules\checkout\ru\controllers;

use common\components\AdmitAd;
use common\components\Criteo;
use common\components\multilocal\Local;
use common\models\Order;
use common\models\ProductVariant;
use common\components\MailChimp;
use frontend\modules\checkout\ru\models\Checkout;
use frontend\modules\checkout\ru\models\pricing\CardOnly;
use Yii;
use yii\helpers\Json;
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
     * @author Dmitry Suvorov <soovorow@gmail.com>
     *
     * @return string
     */
    public function actionIndex()
    {
        AdmitAd::pushCartItems();

        Url::remember();

        if (!Yii::$app->session->has(CardOnly::SESSION_KEY)) {
            CardOnly::apply('true');
        }
        return $this->render('cart');
    }

    /**
     * Checkout page
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     *
     * @return string
     *
     * @throws BadRequestHttpException
     */
    public function actionCheckout()
    {
        /**
         * @var $cart ShoppingCart
         */
        $cart = Yii::$app->cart;

        if ($cart->getCount() <= 0) {
            return $this->redirect('index');
        }

        foreach ($cart->getPositions() as $p) {
            if (!$p->hasInStock()) {
                $cart->remove($p);
            }
        }

        $model = new Checkout();

        if ($model->loadAll(Yii::$app->request->post()) && $order = $model->composeOrder()) {

            $order->sendOrderTo1c();
            $order->sendEmail();

            $order->sendGaTransaction();
            $order->sendAdmitAd();
            $order->sendOrderToMailChimp();
            $order->sendMandrillReviewRequestScheduling();
            $order->setSessionAccess();

            return $this->redirect('https://www.madwave.ru/checkout/cart/checkout-complete?id='.$order->id);
        }

        return $this->render('checkout', ['model' => $model]);
    }

    /**
     * Complete page
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCheckoutComplete($id)
    {
        if (!$order = Order::findOne($id)) {
            throw new NotFoundHttpException;
        }

        $hasSessionAccess = Yii::$app->session->has($order->getSessionAccessKey());
        $invalidUser = Yii::$app->user->isGuest || $order->customer_email !== Yii::$app->user->identity->email;

        if (!$hasSessionAccess && $invalidUser) {
            throw new NotFoundHttpException;
        }

        if (Local::detectLocal() == Local::RU) {
            AdmitAd::setCompletePage($order->getOrderItems(), $order->id, $order->total);
            Criteo::setCompletePage($order->getOrderItems(), $order->id, $order->customer_email);
        }

        return $this->render('complete');
    }

    /**
     * Fill up cart from MailChimp
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     *
     * @param string $uh base64 encoded user email
     * @return string
     */
    public function actionLoadMcCart($uh)
    {
        $r = MailChimp::fillCart($uh);
        return $r ? $this->redirect('/checkout/cart/index') : $this->goHome();
    }

    /**
     * Remove product from cart
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
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
        Yii::$app->cart->remove($p);
        return $this->redirect('index');
    }

    /**
     * Clear cart
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     *
     * @return string
     */
    public function actionClear()
    {
        Yii::$app->cart->removeAll();
        Yii::$app->session->setFlash('info', 'Корзина очищена');
        return $this->redirect('index');
    }
}
