<?php

namespace frontend\modules\citydetector;
/**
 * City detector module
 *
 * Add following code into your `config\main.php` for start using it
 *
 * 'modules' => [
 *     'city_detector' => [
 *         'class' => 'frontend\modules\city_detector\Module',
 *     ],
 * ],
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 *
 */
class CityDetector extends Module
{
    public $city;

    const SESSION_KEY_CITY = 'location_city';
    const SESSION_KEY_REGION = 'location_region';

    public static function getCity()
    {
        if (self::hasLocation()) {
            return \Yii::$app->session->get(self::SESSION_KEY_CITY);
        }
//        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '81.222.228.10';
//        $response = file_get_contents('http://ipgeobase.ru:7020/geo?ip='.$ip);
//        $response = self::w1250_to_utf8(mb_convert_encoding($response, 'HTML-ENTITIES', "windows-1251"));
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
            'Калининград',
            'Кемерово',
            'Киров',
            'Краснодар',
            'Красноярск',
            'Кстово',
            'Курск',
            'Мурманск',
            'Набережные челны',
            'Нижний Новгород',
            'Новосибирск',
            'Омск',
            'Оренбург',
            'Пермь',
            'Ростов-на-Дону',
            'Саратов',
            'Самара',
            'Севастополь',
            'Смоленск',
            'Сургут',
            'Сыктывкар',
            'Тверь',
            'Тольятти',
            'Томск',
            'Тула',
            'Тюмень',
            'Ульяновск',
            'Уфа',
            'Хабаровск',
            'Челябинск',
            'Ярославль',
        ];
    }

    public static function w1250_to_utf8($text) {
        // map based on:
        // http://konfiguracja.c0.pl/iso02vscp1250en.html
        // http://konfiguracja.c0.pl/webpl/index_en.html#examp
        // http://www.htmlentities.com/html/entities/
        $map = array(
            chr(0x8A) => chr(0xA9),
            chr(0x8C) => chr(0xA6),
            chr(0x8D) => chr(0xAB),
            chr(0x8E) => chr(0xAE),
            chr(0x8F) => chr(0xAC),
            chr(0x9C) => chr(0xB6),
            chr(0x9D) => chr(0xBB),
            chr(0xA1) => chr(0xB7),
            chr(0xA5) => chr(0xA1),
            chr(0xBC) => chr(0xA5),
            chr(0x9F) => chr(0xBC),
            chr(0xB9) => chr(0xB1),
            chr(0x9A) => chr(0xB9),
            chr(0xBE) => chr(0xB5),
            chr(0x9E) => chr(0xBE),
            chr(0x80) => '&euro;',
            chr(0x82) => '&sbquo;',
            chr(0x84) => '&bdquo;',
            chr(0x85) => '&hellip;',
            chr(0x86) => '&dagger;',
            chr(0x87) => '&Dagger;',
            chr(0x89) => '&permil;',
            chr(0x8B) => '&lsaquo;',
            chr(0x91) => '&lsquo;',
            chr(0x92) => '&rsquo;',
            chr(0x93) => '&ldquo;',
            chr(0x94) => '&rdquo;',
            chr(0x95) => '&bull;',
            chr(0x96) => '&ndash;',
            chr(0x97) => '&mdash;',
            chr(0x99) => '&trade;',
            chr(0x9B) => '&rsquo;',
            chr(0xA6) => '&brvbar;',
            chr(0xA9) => '&copy;',
            chr(0xAB) => '&laquo;',
            chr(0xAE) => '&reg;',
            chr(0xB1) => '&plusmn;',
            chr(0xB5) => '&micro;',
            chr(0xB6) => '&para;',
            chr(0xB7) => '&middot;',
            chr(0xBB) => '&raquo;',
        );
        return html_entity_decode(mb_convert_encoding(strtr($text, $map), 'UTF-8', 'ISO-8859-2'), ENT_QUOTES, 'UTF-8');
    }
}