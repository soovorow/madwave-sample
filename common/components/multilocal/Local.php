<?php

namespace common\components\multilocal;

class Local {

    const RU = 'ru-RU';
    const EU = 'en-EU';

    public static function choose($params)
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $domain = array_reverse(explode('.', $_SERVER['HTTP_HOST']))[0];
            if ($domain == 'eu') {
                $domain = self::EU;
            }
        } else {
            $domain = self::RU;
        }
        switch ($domain) {
            case self::EU:
                $result = isset($params['en-EU']) ? $params['en-EU'] : null;
                break;
            default:
                $result = isset($params['ru-RU']) ? $params['ru-RU'] : null;
        };
        return $result;
    }

    public static function detectLocal()
    {
        return self::choose([
            self::RU => 'ru-RU',
            self::EU => 'en-EU'
        ]);
    }

    public static function getUrlManager()
    {
        $dir = dirname(dirname(__DIR__)).'/config/';
        return self::choose([
            self::RU => $dir.'url-manager-ru.php',
            self::EU => $dir.'url-manager-en.php'
        ]);
    }

}