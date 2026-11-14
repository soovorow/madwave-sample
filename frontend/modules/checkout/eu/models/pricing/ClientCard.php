<?php

namespace frontend\modules\checkout\eu\models\pricing;

use common\models\ProductVariant;
use yii\base\Model;

/**
 * Client card model
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class ClientCard extends Model implements KeyDiscountInterface
{
    const DISCOUNT = 10;

    const SESSION_KEY = 'card';

    public static function fetchFromSession()
    {
        $session = \Yii::$app->session;

        if (!$session->has(self::SESSION_KEY) && !$session->has('__id')) {
            return false;
        }

        if (!$session->has(self::SESSION_KEY) && $session->has('__id')) {
            $session->set(self::SESSION_KEY, '30000-60000');
        }

        return $session->get(self::SESSION_KEY);
    }

    public static function apply($card_number)
    {
        if (empty($card_number)) {
            return 'Поле "Карта клиента" не может быть пустым значением.';
        }
        if ($promoCode = PromoCode::fetchFromSession()) {
            if ($promoCode->allow_with_card == 0) {
                return 'Примененный вами промокод не позволено использовать вместе со скидкой по карте клиента';
            }
        }
        if (!self::isValid($card_number)) {
            return 'Карта не найдена';
        }
        \Yii::$app->session->set(self::SESSION_KEY, $card_number);
    }

    public static function discard()
    {
        \Yii::$app->session->remove(self::SESSION_KEY);
    }

    public static function getDiscountSum()
    {
        $cart_items = \Yii::$app->cart->getPositions();

        $discount = 0;
        foreach ($cart_items as $v) {
            /** @var $v ProductVariant */
            if (!$v->salePrice && $v->product->discount_card != 1) {
                $discount += ($v->getOldPrice() - $v->getCardPrice()) * $v->getQuantity();
            }
        }
        return $discount;
    }

    public static function isValid($card_number)
    {
        $card = explode('-', $card_number);

        $first = $card[0];
        $second = $card[1];

        $firstRange = ($first >= 20001 && $first <= 61422) && ($second >= 30000 && $second <= 71422);
        $secondRange = ($first >= 10001 && $first <= 19999) && ($second >= 10006 && $second <= 61417);

        if (!$firstRange && !$secondRange) {
            return false;
        }
        return true;
    }
}
