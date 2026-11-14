<?php

namespace frontend\modules\checkout\eu\models\delivery_methods;

use frontend\modules\checkout\eu\models\Checkout;
use yii\db\Query;

class MethodCourier extends DeliveryMethod
{
    public $label = 'Delivery by courier';
    public $description = 'Delivery will take up to 3 days';
    public $code = '"ТГ504249"';

    //Возможно пора переименовать
    public $view = 'checkout___delivery__courier';

    public  $lastName;
    public  $firstName;
    public  $secondName;
    public  $locality;
    public  $address;
    public  $postalCode;
    public  $admin_area;
    public  $_autoComplete;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $session = \Yii::$app->session;
        if ($session->has(Checkout::SESSION_KEY)) {
            $checkout = $session->get(Checkout::SESSION_KEY);
            if (isset($checkout['_delivery'], $checkout['_delivery']->method[2])) {
                $s = $checkout['_delivery']->method[2];
                foreach ($s->attributes as $a => $v) {
                    $this->$a = $v;
                }
            }
        }

    }

    public function getJsInitFunction() {
        return 'methodCourierInit';
    }

    public function getCode($order = false)
    {
        return '"ТГ504249"';
    }

    public function rules()
    {
        return [
            [
                ['address'], 'required'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'address' => 'Address *',
          //  '_autoComplete' => 'Введите название улицы и вашего города *',
        ];
    }

    public function getPostalCode()
    {
        return $this->postalCode;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getSecondName()
    {
        return $this->secondName;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getCost(){
        return 0;
    }

}