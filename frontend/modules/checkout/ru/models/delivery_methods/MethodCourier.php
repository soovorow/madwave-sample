<?php

namespace frontend\modules\checkout\ru\models\delivery_methods;

use common\components\DPDService;
use frontend\modules\checkout\ru\models\Checkout;
use frontend\modules\citydetector\CityDetector;
use yii\db\Query;

class MethodCourier extends DeliveryMethod
{
    public $label = 'Доставка курьером';
    public $description = 'Срок доставки до 3 дней';
    public $code = '"ТГ504249"';
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

    public $admin_area;

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
        $city = $order ? $order->city : $this->locality;

        $spb_places = self::getSPBDeliveryPriceArr();
        if (isset($spb_places[$city])) {
            return '"ТГ504248"';
        }

        $msk_places = self::getMSKDeliveryPriceArr();
        if (isset($msk_places[$city])) {
            return '"ТГ505467"';
        }

        return '"ТГ504249"';
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
        $min_sum = 200;
        $city_name = CityDetector::getCity();

        if ($city_name == 'Москва') {
            $min_sum = 400;
        } elseif ($city_name !== 'Санкт-Петербург') {
            $min_sum = 400;
        }

        return $this->description.', от '.$min_sum.'р';
    }

    public function getCost()
    {
        $spb_places = self::getSPBDeliveryPriceArr();
        if (isset($spb_places[$this->locality])) {
            return $spb_places[$this->locality];
        }

        $msk_places = self::getMSKDeliveryPriceArr();
        if (isset($msk_places[$this->locality])) {
            return $msk_places[$this->locality];
        }

        return 800;

        $city = (new Query())
            ->select('cityId, cityName')
            ->from('dpd_city_list')
            ->where(['cityName' => $this->locality])
            ->one(\Yii::$app->db);

        if ($city) {
            $arData = [
                'delivery' => [
                    'cityId' => $city['cityId']
                ],
                'from' => [
                    'cityId' => 49694167,
                ],
                'weight' => self::getOrderWeight(),
                'test' => 0
            ];
            $DPD = new DPDService();
            $cost = $DPD->getServiceCost($arData);
            if (isset($cost['cost']) && $cost['cost'] > 0) {
                return $cost['cost'];
            }
        }

        return false;
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

    private function getOrderWeight()
    {
        $products = \Yii::$app->cart->getPositions();

        $weight = 0;
        foreach ($products as $product) {
            $weight += $product->weight * $product->getQuantity();
        }
        return $weight;

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

    private function getSPBDeliveryPriceArr()
    {
        return [
            'Санкт-Петербург' => 200,
            'Санкт-петербург' => 200,
            'Санкт Петербург' => 200,
            'Санкт петербург' => 200,
            'Saint-Petersburg' => 200,
            'Saint Petersburg' => 200,
            'Кудрово' => 200,
            'Агалатово' => 450,
            'Александровская' => 400,
            'Белоостров' => 450,
            'Большая Ижора' => 600,
            'Вартемяги' => 400,
            'Волосово'=> 800,
            'Волхов' => 1500,
            'Всеволожск' => 400,
            'Выборг' => 1500,
            'Гарболово' => 600,
            'Гатчина' => 450,
            'Горская' => 400,
            'Зеленогорск' => 600,
            'Каменногорск'=> 1900,
            'Кипень' => 450,
            'Кириши' => 1500,
            'Кировск' => 600,
            'Колпино' => 400,
            'Колтуши' => 400,
            'Комарово' => 600,
            'Коммунар' => 450,
            'Кранштадт' => 600,
            'Красное село' => 450,
            'Кузьмолово' => 400,
            'Лахта' => 350,
            'Левашово' => 350,
            'Лемболово' => 600,
            'Лесколово' => 450,
            'Лисий Нос ' => 400,
            'Ломоносов' => 450,
            'Любань' => 800,
            'Мга' => 600,
            'Металлострой' => 400,
            'Никольское ' => 450,
            'Новое Девяткино' => 350,
            'Ольгино' => 350,
            'Осельки' => 450,
            'Отрадное' => 450,
            'Павловск' => 450,
            'Первомайское' => 800,
            'Песочный' => 400,
            'Петергоф' => 450,
            'Порошкино' => 350,
            'им. Морозова' => 600,
            'им. Свердлова' => 400,
            'Приозерск' => 1900,
            'Пушкин' => 400,
            'Разметелево' => 400,
            'Рахья' => 450,
            'Репино' => 600,
            'Романовка' => 400,
            'Ропша' => 450,
            'Рощино' => 800,
            'Светогорск' => 1900,
            'Серолово' => 400,
            'Сестрорецк' => 450,
            'Сиверский пос.' => 800,
            'Солнечное ' => 450,
            'Сосново' => 800,
            'Сосновый Бор' => 1200,
            'Стрельна' => 400,
            'Токсово' => 400,
            'Тосно' => 600,
            'Шлиссельбург' => 600,
            'Шушары' => 350,
            'Юкки' => 350,
        ];
    }

    private function getMSKDeliveryPriceArr()
    {
        return [
            'Москва' => 400,
            'Moscow' => 400,
            'Реутов' => 500,
            'Люберцы' => 500,
            'Хики' => 500,
            'Красногорск' => 500,
            'Одинцово' => 500,
            'Долгопрудный' => 500,
            'Балашиха' => 500,
            'Щербинка' => 500,
            'Железнодорожный' => 500,
            'Королев' => 500,
            'Лыткарино' => 500,
            'Лобня' => 500,
            'Домодедово' => 500,
            'Подольск' => 700,
            'Троицк' => 700,
            'Пушкино' => 700,
            'Щелково' => 700,
            'Жуковский' => 700,
            'Климовск' => 700,
            'Фрязино' => 700,
            'Раменское' => 700,
            'Истра' => 1000,
            'Ногинск' => 1000,
            'Бронницы' => 1000,
            'Звенигород' => 1000,
            'Солнечногорск' => 1000,
            'Дмитров' => 1000,
            'Чехов' => 1000,
            'Сергиев Посад' => 1200,
            'Наро-Фоминск' => 1200,
            'Павловский Посад' => 1200,
            'Клин' => 1200,
            'Серпухов' => 1200,
            'Воскресенск' => 1200,
            'Орехово-Зуево' => 1500,
            'Руза' => 1500,
            'Ступино' => 1500,
            'Можайск' => 1500,
            'Коломна' => 1500,
            'Волоколамск' => 1500,
            'Дубна' => 1500,
            'Егорьевск' => 1500,
            'Видное' => 500,
        ];
    }
}