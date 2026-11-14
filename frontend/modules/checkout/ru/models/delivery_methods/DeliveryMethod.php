<?php

namespace frontend\modules\checkout\ru\models\delivery_methods;

use frontend\modules\checkout\ru\models\steps\Checkout;
use yii\base\Model;
use yii\widgets\ActiveForm;

abstract class DeliveryMethod extends Model implements DeliveryMethodInterface
{
    const SESSION_KEY = 'delivery_cost';

    const SESSION_CONFIRM_COST = 'delivery_confirm_cost';

    const SESSION_METHOD = 'delivery_method_key';

    public $label;
    public $description;
    public $code;
    public $view;
    public $locality;
    public $admin_area;

    public $_delivery_key;

    public function requiredWhenClient() {
        return
            'function (attribute, value) {
                return $("[name=\"Delivery[delivery_method]\"]:checked").val()
                 == '.$this->getDeliveryKey().';
            }';
    }

    public function getDeliveryKey() {
        return $this->_delivery_key;
    }

    public function setDeliveryKey($key) {
        $this->_delivery_key = $key;
    }

    public function getView() {
        return $this->view;
    }

    public function getLabel() {
        return $this->label;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getCode($order = false) {
        return $this->code;
    }

    public function setDeliveryCostInSession()
    {
        $cost = $this->getCost();
        if ($cost !== false) {
            \Yii::$app->session->set($this::className(), $cost);
            \Yii::$app->session->set(self::SESSION_KEY, $cost);
        } else {
            \Yii::$app->session->set(self::SESSION_KEY, 'fuck');
        }
    }

    public static function getDeliveryCost($currency = false) {
        $cost = \Yii::$app->session->get(DeliveryMethod::SESSION_KEY);
        if (!$cost) {
            return null;
        }
        return $cost . ($currency ?  \Yii::$app->params['currency'] : null);
    }

}