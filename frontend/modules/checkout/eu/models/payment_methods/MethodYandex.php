<?php

namespace frontend\modules\checkout\eu\models\payment_methods;

use frontend\modules\checkout\eu\models\pricing\CardOnly;

class MethodYandex extends PaymentMethod
{
    public $label = 'Pay online';

    public $description = 'Оплата заказа онлайн через сервис "Яндекс.Касса"';

    public $code = 'ТГ505444';

    public function getDescription()
    {
        return $this->description . '<br>-'.CardOnly::DISCOUNT.'% при оплате онлайн';
    }
}