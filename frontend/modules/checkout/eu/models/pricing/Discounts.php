<?php

namespace frontend\modules\checkout\eu\models\pricing;

class Discounts
{
    public static function getTotalDiscountSum() {
        $discounts = [
            self::getTotalCardDiscountSum(),
            self::getTotalCodeDiscountSum(),
            self::getCardOnlyDiscountSum(),
        ];
        return array_sum($discounts);
    }

    public static function getCardOnlyDiscountSum($currency = false)
    {
        if (!CardOnly::fetchFromSession()) return 0;
        return (CardOnly::getDiscountSum())
            . ($currency ?  \Yii::$app->params['currency'] : null);
    }

    public static function getTotalCardDiscountSum($currency = false)
    {
        if (!ClientCard::fetchFromSession()) return 0;
        return ClientCard::getDiscountSum()
            . ($currency ?  \Yii::$app->params['currency'] : null);
    }

    public static function getTotalCodeDiscountSum($currency = false)
    {
        if (!PromoCode::fetchFromSession()) return 0;
        return PromoCode::getDiscountSum()
            . ($currency ?  \Yii::$app->params['currency'] : null);
    }
}