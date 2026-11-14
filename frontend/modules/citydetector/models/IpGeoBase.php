<?php

namespace frontend\modules\citydetector\models;

class IpGeoBase
{
    const SESSION_KEY_CITY = 'location_city';
    const SESSION_KEY_REGION = 'location_region';

    public static function getCity()
    {
        if (self::hasLocation()) {
            return \Yii::$app->session->get(self::SESSION_KEY_CITY);
        }
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '81.222.228.10';

//        $response = file_get_contents('http://ipgeobase.ru:7020/geo?ip='.$ip);
//        $response = mb_convert_encoding($response, 'HTML-ENTITIES', "windows-1251");
//        preg_match('/<city[^>]*>(.*?)<\/city>/si', $response, $city);
//        preg_match('/<region[^>]*>(.*?)<\/region>/si', $response, $region);
//        \Yii::$app->session->set(self::SESSION_KEY_CITY, $city[1]);
//        \Yii::$app->session->set(self::SESSION_KEY_REGION, $region[1]);
        return 'Санкт-Петербург';
    }

    public static function hasLocation() {
        return \Yii::$app->session->has(self::SESSION_KEY_CITY);
    }

    public static function setLocation($city, $region = null) {
        \Yii::$app->session->set(self::SESSION_KEY_CITY, $city);
        if ($region !== null) {
            \Yii::$app->session->set(self::SESSION_KEY_REGION, $region);
        }
    }
    
    public static function highestCities() {
        return [
            'Москва',
            'Санкт-Петербург',
            'Архангельск',
            'Владивосток',
            'Волгоград',
            'Воронеж',
            'Екатеринбург',
            'Ижевск',
            'Иркутск',
            'Казань',
            'Кемерово',
            'Краснодар',
            'Красноярск',
            'Мурманск',
            'Набережные челны',
            'Нижний Новгород',
            'Новосибирск',
            'Омск',
            'Пермь',
            'Ростов-на-Дону',
            'Саратов',
            'Самара',
            'Сургут',
            'Тверь',
            'Тольятти',
            'Тула',
            'Тюмень',
            'Ульяновск',
            'Уфа',
            'Хабаровск',
            'Челябинск',
            'Ярославль',
        ];
    }
}
