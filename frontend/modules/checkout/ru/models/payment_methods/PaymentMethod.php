<?php

namespace frontend\modules\checkout\ru\models\payment_methods;

use yii\base\Model;

abstract class PaymentMethod extends Model implements PaymentMethodInterface
{
    public $label;
    
    public $description;
    
    public $code;

    public function getLabel() {
        return $this->label;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getCode() {
        return $this->code;
    }
}