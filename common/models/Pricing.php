<?php
namespace common\models;

use common\components\multilocal\Local;
use frontend\modules\checkout\ru\models\pricing\CardOnly;
use frontend\modules\checkout\ru\models\pricing\ClientCard;
use frontend\modules\checkout\ru\models\pricing\Discounts;
use frontend\modules\checkout\ru\models\pricing\PromoCode;
use Yii;
use yii\base\Model;

/**
 * Helper model for deal with Pricing
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Pricing extends Model
{

    /**
     * @param $v ProductVariant
     * @return float|int|null
     * @internal param bool $ignore_s
     */
    public static function getItemSumWithDiscount($v)
    {
        $discount = 0;

        if ($v->getSalePrice()) {
            return $v->price;
        }


        if (Local::detectLocal() == Local::RU && Yii::$app->session->has(ClientCard::SESSION_KEY) && $v->product->discount_card != 1) {
            $special = $v->product->discount_card_percent;
            $default = ClientCard::DISCOUNT;
            $discount += ($special > $default ? $special : $default);
        }

        if ($co = CardOnly::fetchFromSession()) {
//            $discount += CardOnly::DISCOUNT;
        }

        if ($promo = PromoCode::fetchFromSession()) {
            /** @var $promo PromoCode */
            if ($promo->type == $promo::TYPE_DISCOUNT) {
                $discount += $promo->value;
            } elseif ($promo->type = $promo::TYPE_VOUCHER) {

            }
        }

        $price = $v->price - ($v->price / 100 * $discount);

        return $price;
    }

    public static function getItemSumClass($v) {
        if ($v->salePrice) {
            return 'color-red';
        }

        if (Yii::$app->session->has(ClientCard::SESSION_KEY) && $v->product->discount_card != 1) {
            return 'color-primary';
        }

        return '';
    }

    public static function getPreparedSum() {

        $session = Yii::$app->session;

        $products = \Yii::$app->cart->getPositions();

        $sum = 0;

        foreach ($products as $product) {
            $sum += Pricing::getItemSumWithDiscount($product) * $product->getQuantity();
        }

        if ($session->has(PromoCode::SESSION_KEY)) {
            $sum += abs(Discounts::getTotalCodeDiscountSum());
        }

        if ($session->has(ClientCard::SESSION_KEY)) {
            $sum += abs(Discounts::getTotalCardDiscountSum());
        }

        return $sum;
    }

}
