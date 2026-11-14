<?php

namespace frontend\modules\checkout\ru\controllers;

use common\models\ProductVariant;
use frontend\modules\checkout\ru\models\Checkout;
use frontend\modules\checkout\ru\models\Delivery;
use frontend\modules\checkout\ru\models\delivery_methods\DeliveryMethod;
use frontend\modules\checkout\ru\models\delivery_methods\DeliveryMethodInterface;
use frontend\modules\checkout\ru\models\delivery_methods\MethodCourier;
use frontend\modules\checkout\ru\models\delivery_methods\MethodPickup;
use frontend\modules\checkout\ru\models\pricing\PromoCode;
use frontend\modules\checkout\ru\models\pricing\CardOnly;
use frontend\modules\checkout\ru\models\pricing\ClientCard;
use frontend\widgets\CartCounter;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

/**
 * Ajax controller
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class AjaxController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $this->layout = 'empty';
            return true;
        }
        return false;
    }

    public function actionSaveCheckoutFormInSession() {
        $m = \Yii::$app->request->post('model');

        if (!$m) {
            throw new BadRequestHttpException();
        }

        parse_str($m, $post);

        $model = new Checkout();
        $deliveryIsLoaded = $model->_delivery->load($post);
        $customerIsLoaded = $model->_customer->load($post);
        $paymentIsLoaded = $model->_payment->load($post);

        if ($customerIsLoaded && $deliveryIsLoaded && $paymentIsLoaded) {
            /** @var $deliveryMethod DeliveryMethod */;
            $method = (int) $model->_delivery->delivery_method;
            if (is_int($method)) {
                $deliveryMethod = $model->_delivery->method[$model->_delivery->delivery_method];
                $deliveryMethod->load($post);
            }
            $model->saveInSession();
        }
    }

    public function actionSetDeliveryCost() 
    {
        /** @var $method DeliveryMethodInterface */
        if (!isset($_POST['method'])) {
            throw new BadRequestHttpException;
        }
        $courier = new MethodCourier();
        $session = \Yii::$app->session;
        $method = (new Delivery())->method[$_POST['method']];
        if ($method->getCode() === $courier->getCode()) {
            if ($session->has($courier::className())) {
                $session->set(DeliveryMethod::SESSION_KEY, $session->get($courier::className()));
            } else {
                $session->set(DeliveryMethod::SESSION_KEY, 'fuck');
            }
        } else {
            $method->setDeliveryCostInSession();
        }
        return Json::encode([
            'check' => $this->render('../cart/checkout__check'),
        ]);
    }
    
    public function actionLoadCourierPrice() 
    {
        $request = \Yii::$app->request;
        $locality = $request->post('locality');
        $admin_area = $request->post('admin_area');
        $m = new MethodCourier();
        $m->locality = $locality;
        $m->admin_area = $admin_area;
        $m->setDeliveryCostInSession();
        return Json::encode([
            'check' => $this->render('../cart/checkout__check'),
        ]);
    }



    public function actionLoadPickUpRadioList()
    {
        return MethodPickup::LoadPickUpRadioList($this);
    }

    public function actionRefreshCart()
    {
        return CartCounter::widget();
    }

    public function actionChangeQuantity()
    {
        $request = \Yii::$app->request;
        $sku = $request->post('sku');
        $quantity = $request->post('quantity');
        if (!$sku || !$quantity || !$v = ProductVariant::findOne($sku)) {
            throw new BadRequestHttpException;
        }
        \Yii::$app->cart->update($v, $quantity);

        return $this->renderCartCheck();
    }

    public function actionRefreshCartTable()
    {
        return Json::encode(['list' => $this->renderCartTable()]);
    }

    public function actionRefreshCheckoutWidget()
    {
        return Json::encode([
            'check' => $this->renderCartCheck(CardOnly::apply(\Yii::$app->request->post('val'))),
            'list' => $this->renderCartTable()
        ]);
    }

    public function actionRefreshCheckoutFinalWidget() {
        CardOnly::apply(\Yii::$app->request->post('val'));
        return Json::encode([
            'check' => $this->render('../cart/checkout__check'),
        ]);
    }

    public function actionCheckCoupon()
    {
        return Json::encode([
            'check' => $this->renderCartCheck(PromoCode::apply(\Yii::$app->request->post(PromoCode::SESSION_KEY))),
            'list' => $this->renderCartTable()
        ]);
    }

    public function actionCheckCard()
    {
        return Json::encode([
            'check' => $this->renderCartCheck(ClientCard::apply(\Yii::$app->request->post(ClientCard::SESSION_KEY))),
            'list' => $this->renderCartTable()
        ]);
    }

    public function actionRemoveCoupon()
    {
        return Json::encode([
            'check' => $this->renderCartCheck(PromoCode::discard()),
            'list' => $this->renderCartTable()
        ]);
    }

    public function actionRemoveCard()
    {
        return Json::encode([
            'check' => $this->renderCartCheck(ClientCard::discard()),
            'list' => $this->renderCartTable()
        ]);
    }

    private function renderCartCheck($error_msg = false)
    {
        return $this->render('../cart/cart__check', ['error_msg' => $error_msg]);
    }

    private function renderCartTable()
    {
        return $this->render('../cart/cart__table');
    }
}

