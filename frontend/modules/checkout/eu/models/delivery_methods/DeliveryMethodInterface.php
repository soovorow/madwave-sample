<?php

namespace frontend\modules\checkout\eu\models\delivery_methods;

interface DeliveryMethodInterface
{
    public function getLabel();

    public function getDescription();

    public function getCode($order = false);

    public function getPostalCode();

    public function setDeliveryCostInSession();

    public function getJsInitFunction();

    public function getFirstName();

    public function getLastName();

    public function getSecondName();
}