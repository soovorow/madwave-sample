<?php
/**
 * Created by PhpStorm.
 * User: coder
 * Date: 06.03.2017
 * Time: 16:54
 */

namespace frontend\modules\checkout\eu\models\payment_methods;


class MethodOnline extends PaymentMethod
{
    public $label = 'Pay online';

    public $type = 'terminal';

    public $widgetClass = 'Paytrail';

    public $description = 'Pay using "Paytrail"';

    public $code = 'ТГ507169';

    public function getDescription()
    {
        return $this->description;
    }
}