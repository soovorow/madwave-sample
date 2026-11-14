<?php

use common\components\multilocal\Local;

return [
    'adminEmail' => 'admin@example.com',
    'baseUrl' => YII_ENV_DEV ? Local::choose([
        Local::RU => 'http://dev.madwave.ru',
        Local::EU => 'http://dev.madwave.eu',
    ]) : Local::choose([
        Local::RU => 'https://www.madwave.ru',
        Local::EU => 'http://madwave.eu',
    ])
];
