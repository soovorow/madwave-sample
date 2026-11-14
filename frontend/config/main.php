<?php
use common\components\multilocal\Local;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'site/index',
    'modules' => [
        'citydetector' => [
            'class' => 'frontend\modules\citydetector\Module',
        ],
        'checkout' => [
            'class' => Local::choose([
                Local::RU => 'frontend\modules\checkout\ru\Module',
                Local::EU => 'frontend\modules\checkout\eu\Module',
            ]),
        ],
    ],
    'components' => [
        'ga' => [
            'class' => 'baibaratsky\yii\google\analytics\MeasurementProtocol',
            'trackingId' => Local::choose([
                Local::RU => 'UA-35217976-1',
                Local::EU => 'UA-82641409-1',
            ]),
            'useSsl' => true,
            'overrideIp' => true,
            'asyncMode' => true,
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [

                'vkontakte' => [
                    'class' => 'yii\authclient\clients\VKontakte',
                    'clientId' => '5630128',
                    'clientSecret' => 'REDACTED_BEFORE_PUBLICATION',
                    'scope' => ['email']
                ],
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => '1221291301225236',
                    'clientSecret' => 'REDACTED_BEFORE_PUBLICATION',
                ],
                'google' => [
                    'class' => 'yii\authclient\clients\Google',
                    'clientId' => 'REDACTED_BEFORE_PUBLICATION',
                    'clientSecret' => 'REDACTED_BEFORE_PUBLICATION',
                ],

            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'cart' => [
            'class' => 'yz\shoppingcart\ShoppingCart',
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'appendTimestamp' => true,
            'bundles' => require __DIR__ . '/assets-prod.php',
        ],
        'log' => [
            'traceLevel' => 3,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'enabled' => true,
                    'levels' => ['error', 'warning'],
                ],
//                [
//                    'class' => 'common\components\SlackTarget',
//                    'levels' => ['error', 'warning'],
//                    'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'],
//                    'enabled' => !YII_DEBUG,
//                    'webhookUrl' => 'https://hooks.slack.com/services/REDACTED_BEFORE_PUBLICATION',
//                    'username' => 'MadWave Store',
//                    'icon_emoji' => ':poop:',
//                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,

];
