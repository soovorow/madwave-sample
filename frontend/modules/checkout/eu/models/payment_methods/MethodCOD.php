<?php

namespace frontend\modules\checkout\eu\models\payment_methods;

/**
 * @TODO if postRF - $this else ТГ506723
 */
class MethodCOD extends PaymentMethod
{
    public $label = 'Наличными при получении';

    public $description = 'Оплата заказа наличными при получении заказа';

    public $code = 'ТГ505443';

}