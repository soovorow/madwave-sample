<?php

namespace frontend\modules\checkout\ru\models\delivery_methods;

interface DeliveryMethodInterface
{
    public function getLabel();

    public function getDescription();

    public function getCode($order = false);

    public function getCost();

    public function getPostalCode();

    public function getCity();

    public function getAddress();

    public function setDeliveryCostInSession();

    public function getJsInitFunction();

    public function getFirstName();

    public function getLastName();

    public function getSecondName();
}