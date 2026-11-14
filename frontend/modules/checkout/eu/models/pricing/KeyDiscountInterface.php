<?php

namespace frontend\modules\checkout\eu\models\pricing;

interface KeyDiscountInterface
{
    public static function fetchFromSession();

    public static function apply($key);

    public static function discard();

    public static function getDiscountSum();
}