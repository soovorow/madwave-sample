<?php


use common\components\multilocal\Local;

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => Local::detectLocal(),
    'sourceLanguage' => 'en-EU',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'translate' => [
            'class' => 'richweber\google\translate\Translation',
            'key' => 'REDACTED_BEFORE_PUBLICATION',
        ],
        'reCaptcha' => [
            'name' => 'reCaptcha',
            'class' => 'himiklab\yii2\recaptcha\ReCaptcha',
            'siteKey' => 'REDACTED_BEFORE_PUBLICATION',
            'secret' => 'REDACTED_BEFORE_PUBLICATION',
        ],
        'urlManager' => [
            'class' => 'common\components\multilocal\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    //'basePath' => '@app/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
    ],
];
