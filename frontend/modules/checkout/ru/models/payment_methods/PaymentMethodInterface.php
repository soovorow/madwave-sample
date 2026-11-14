<?php

namespace frontend\modules\checkout\ru\models\payment_methods;

interface PaymentMethodInterface
{
    public function getLabel();

    public function getDescription();

    public function getCode();
}