<?php

namespace frontend\modules\checkout\eu\models;

use common\components\postifi\services\LetterEconomyWithCOD;
use common\components\postifi\services\LetterExpress;
use common\components\postifi\services\ParcelExpress;
use common\components\postifi\services\PostiServiceInterface;
use frontend\modules\checkout\eu\models\delivery_methods\DeliveryMethodInterface;
use frontend\modules\checkout\eu\models\delivery_methods\MethodCourier;
use yii\base\Model;
use yii\bootstrap\ActiveForm;

/**
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Delivery extends Model implements DeliveryMethodInterface
{
    const SESSION_KEY = 'delivery';

    public $delivery_method;

    public $method;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

//        $this->method[] = new MethodCourier(['_delivery_key' => 1]);
        $this->method[] = new ParcelExpress(['_delivery_key' => 1]);
//        $this->method[] = new LetterEconomyWithCOD(['_delivery_key' => 2]);


        $weight = 0;

        $cart = \Yii::$app->cart;
        foreach ($cart->positions as $position) {
            $weight += $position->getWeight() * $position->getQuantity();
        }

        foreach ($this->method as $k => $service) {
            /**
             * @var $service PostiServiceInterface
             */
            $service->setItemsCount($cart->getCount());
            $service->setItemsWeight($weight);

            $available = true;
            $price = $service->getPrice();

            if ($weight > $service->getMinimumWeight()) {
                if (!is_null($service->getMaximumWeight())) {
                    if ($weight > $service->getMaximumWeight()) {
                        $available = false;
                    }
                }
            } else {
                $available = false;
            }

            if (empty($price) || !($price > 0)) {
                $available = false;
            }

//            if (!$available) {
//                unset($this->method[$k]);
//            }
        }


        $session = \Yii::$app->session;
        if ($session->has(Checkout::SESSION_KEY)) {
            $checkout = $session->get(Checkout::SESSION_KEY);
            if (isset($checkout['_delivery'])) {
                $s = $checkout['_delivery'];
                $this->delivery_method = $s->delivery_method;
            }
        }
    }

    public function rules()
    {
        return [
            [
                ['delivery_method'],
                'required'
            ],
            ['method', 'validateMethod']
        ];
    }

    public function validateMethod() {
        if (!isset($this->delivery_method) || !$this->method[$this->delivery_method]->validate()) {
            $this->addError('method');
        }
    }


    public function attributeLabels()
    {
        return [
            'delivery_method' => 'Shipping method',
            'delivery_lastname' => 'Фамилия *',
            'delivery_firstname' => 'Имя *',
            'delivery_secondname' => 'Отчество *',
            'delivery_street' => 'Street *',
            'delivery_house' => 'Номер дома *',
            '_delivery_full_address' => 'Введите название улицы',
            'delivery_flour' => 'Этаж',
            'delivery_apartment' => 'Квартира',
            'delivery_index' => 'Индекс',
            'delivery_locality' => 'Населенный пункт *',
            '_search' => 'Поиск точек самовывоза',
        ];
    }


    /**
     * @param $form ActiveForm
     * @return string
     */
    public static function widget($model, $form)
    {
        $delivery_methods = [];

        foreach ($model->method as $i => $d) {
            /** @var $method DeliveryMethodInterface */
            $method = new $d;
            $delivery_methods[$i] = self::wrapRadio($method->getLabel(), $method->getDescription());
        }

        return \Yii::$app->controller->view->render('checkout___delivery', [
            'form' => $form,
            'model' => $model,
            'delivery_methods' => $delivery_methods,
        ]);
    }

    public static function getAttrFromSession($attr)
    {
        $s = \Yii::$app->session;
        $k = self::SESSION_KEY;
        $a = [];

        if ($s->has($k)) {
            $a = $s->get($k);
        }

        return isset($a[$attr]) ? $a[$attr] : null;
    }

    private function wrapRadio($label, $description) {
        return '<span><i></i><span class="l">'.$label.'</span><small>'.$description.'</small></span>';
    }

    public function getLabel()
    {
        return $this->method[$this->delivery_method]->getLabel();
    }

    public function getDescription()
    {
        return $this->method[$this->delivery_method]->getDescription();
    }

    public function getCode($order = false)
    {
        return $this->method[$this->delivery_method]->getCode($order);
    }

    public function setDeliveryCostInSession()
    {
        return $this->method[$this->delivery_method]->setDeliveryCostInSession();
    }

    public function getJsInitFunction()
    {
        return $this->method[$this->delivery_method]->getJsInitFunction();
    }

    public function getCity()
    {
        return $this->method[$this->delivery_method]->getCity();
    }

    public function getAddress()
    {
        return $this->method[$this->delivery_method]->getAddress();
    }

    public function getPostalCode()
    {
        return $this->method[$this->delivery_method]->getPostalCode();
    }

    public function getFirstName()
    {
        return $this->method[$this->delivery_method]->getFirstName();
    }

    public function getLastName()
    {
        return $this->method[$this->delivery_method]->getLastName();
    }

    public function getSecondName()
    {
        return $this->method[$this->delivery_method]->getSecondName();
    }

    public function getCost()
    {
        return $this->method[$this->delivery_method]->getCost();
    }

    public function getCountry()
    {
        return $this->method[$this->delivery_method]->getCountry();
    }
}