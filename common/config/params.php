<?php

use common\components\multilocal\Local;

return [
    'adminEmail' => 'managerinternet@madwave.ru',
    'supportEmail' => 'managerinternet@madwave.ru',

    '1c.order_export_dir' => '/var/www/admin/www/madwave.ru/sites/default/files/1c_output/',
    '1c.track_code_export_dir' => '/var/www/admin/www/yii2.madwave.ru/frontend/web/track/',

    'user.passwordResetTokenExpire' => 3600,
    'user.emailChangeTokenExpire' => 3600,

    'orderNotificationBcc' => 'servicemad@yandex.ru',

    'admitad' => [
        'campaign_code' => '41693e22f1',
        'postback_key' => 'REDACTED_BEFORE_PUBLICATION',
    ],

    'googleMapJs' => http_build_query([
        'language' => 'ru-RU',
        'key' => 'REDACTED_BEFORE_PUBLICATION',
        'libraries' => 'places',
        'sensor' => 'false',
        'callback' => 'gmapInitializeer'
    ]),

    'currency' => Local::choose([
        Local::RU => 'руб.',
        Local::EU => '<i class="fa fa-eur"> </i> ',
    ]),

    'highlyDiscount' => '55%',

    'baseUrl' => YII_ENV_DEV ? Local::choose([
        Local::RU => 'http://dev.madwave.ru',
        Local::EU => 'http://dev.madwave.eu',
    ]) : Local::choose([
        Local::RU => 'https://www.madwave.ru',
        Local::EU => 'https://madwave.eu',
    ])
];
