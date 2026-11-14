<?php

namespace frontend\modules\checkout\ru\models\delivery_methods;

use common\models\Order;
use frontend\modules\checkout\ru\models\Checkout;

class MethodFirstClassPostRF extends DeliveryMethod
{
    public $view = 'checkout___delivery__mail';

    public $lastName;
    public $firstName;
    public $secondName;
    public $locality;
    public $street;
    public $house;
    public $apartment;
    public $flour;
    public $postalCode;

    public $_autoComplete;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $session = \Yii::$app->session;
        if ($session->has(Checkout::SESSION_KEY)) {
            $checkout = $session->get(Checkout::SESSION_KEY);
            if (isset($checkout['_delivery'], $checkout['_delivery']->method[1])) {
                $s = $checkout['_delivery']->method[3];
                foreach ($s->attributes as $a => $v) {
                    $this->$a = $v;
                }
            }
        }

    }


    public function getLabel()
    {
        return 'Почта россии 1й класс';
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
        return 'Срок доставки от 7 до 10 рабочих дней , '.($cost == 0 ? 'бесплатно' : ($cost.'р'));
    }

    /**
     * @param bool|Order|false $order Order|false
     * @return string
     */
    public function getCode($order = false)
    {
        $code = '"ТГ507103"';

        return $code;
    }

    public function getCost()
    {
        $cost = 430;

        return $cost;
    }

    public function attributeLabels()
    {
        return [
            'lastName' => 'Фамилия *',
            'firstName' => 'Имя *',
            'secondName' => 'Отчество *',
            'locality' => 'Населенный пункт *',
            'street' => 'Улица *',
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