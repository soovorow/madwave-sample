<?php

namespace frontend\modules\checkout\eu\models\delivery_methods;

use common\models\Order;
use frontend\modules\checkout\eu\models\Checkout;
use yii\helpers\Json;

class MethodMail extends DeliveryMethod
{
    const BREAK_POINT = 4000;

    public $label = 'Почта россии';
    public $description = 'Срок доставки до 2 недель';
    public $view = 'checkout___delivery__mail';

    public  $lastName;
    public  $firstName;
    public  $secondName;
    public  $locality;
    public  $street;
    public  $house;
    public  $apartment;
    public  $flour;
    public  $postalCode;

    public  $_autoComplete;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $session = \Yii::$app->session;
        if ($session->has(Checkout::SESSION_KEY)) {
            $checkout = $session->get(Checkout::SESSION_KEY);
            if (isset($checkout['_delivery'], $checkout['_delivery']->method[1])) {
                $s = $checkout['_delivery']->method[1];
                foreach ($s->attributes as $a => $v) {
                    $this->$a = $v;
                }
            }
        }

    }

    public function getJsInitFunction() {
        return 'methodMailInit';
    }

    public function rules()
    {
        return [
            [
                [
//                    'lastName',
//                    'firstName',
//                    'secondName',
                    'locality',
                    'street',
                    'house',
                    'postalCode',
//                    '_autoComplete',

                ],
                'required',
                'whenClient' => $this->requiredWhenClient()
            ],
            [
                [
                    'lastName',
                    'firstName',
                    'secondName',
                ],
                'trim'
            ],
            [
                [
                    'apartment',
                    'flour',
                ],
                'default'
            ],
        ];
    }

    public function getDescription()
    {
        $cost = self::getCost();
        return $this->description.', '.($cost == 0 ? 'бесплатно' : ($cost.'р'));
    }

    /**
     * @param bool|Order|false $order Order|false
     * @return string
     */
    public function getCode($order = false)
    {
        $code = '"Ф0001789"';

        $total = $order ? $order->total : Checkout::getTotalSum(false, false);

        if ($total > self::BREAK_POINT) {
            $code = '"ТГ504646"';
        }
        return $code;
    }

    public function getCost()
    {
        $cost = 280;

        if (Checkout::getTotalSum(false, false) > self::BREAK_POINT) {
            $cost = 0;
        }
        return $cost;
    }

    public function attributeLabels()
    {
        return [
            'lastName' => 'Фамилия *',
            'firstName' => 'Имя *',
            'secondName' => 'Отчество *',
            'locality' => 'Населенный пункт *',
            'street' => 'Street *',
            'house' => 'Дом *',
            'apartment' => 'Квартира',
            'flour' => 'Этаж',
            'postalCode' => 'Почтовый индекс *',
            '_autoComplete' => 'Введите название улицы и вашего города *',
        ];
    }

    public function getCity()
    {
        return $this->locality;
    }

    public function getAddress()
    {
        return $this->street .' '. $this->house.' - '. $this->apartment.(empty($this->flour) ? '' : (', этаж - '.$this->flour));
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
}