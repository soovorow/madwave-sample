<?php
use common\components\multilocal\Local;

return [
    'adminEmail' => 'managerinternet@madwave.ru',
    'baseUrl' => Local::choose([
        Local::RU => 'https://www.madwave.ru',
        Local::EU => 'http://dev.madwave.eu',
    ]),
];
