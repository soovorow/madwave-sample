<?php

namespace frontend\modules\checkout\eu\models\pricing;

use yii\base\Model;

/**
 * Card only discount model
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class CardOnly extends Model implements KeyDiscountInterface
{
    const DISCOUNT = 3;

    const SESSION_KEY = 'card_only_discount';

    public static function fetchFromSession()
    {
        $session = \Yii::$app->session;

        if (!($session->has(self::SESSION_KEY) && $session->get(self::SESSION_KEY) == 1)) {
            return false;
        }

        return $session->get(self::SESSION_KEY);
    }

    public static function apply($key)
    {
        \Yii::$app->session->set('card_only_discount', $key == 'false' ? 0 : 1);
    }

    public static function discard()
    {
        \Yii::$app->session->remove(self::SESSION_KEY);
    }

    public static function getDiscountSum() {
        return \Yii::$app->cart->getCost() / 100 * self::DISCOUNT;
    }

}
