<?php

namespace common\models\sale;

use common\models\Product;
use common\models\ProductVariant;
use Yii;
use yii\base\Model;

/**
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Winter17_18 extends Model
{
    public $common_general_discount = 10;
    public $sale_general_discount = 25;

    public static function cronJob()
    {
        $m = new Winter17_18();
        $m->disableDiscounts();
    }

    public static function isEnabled()
    {
        return false;
    }


    private function getCurrentTime()
    {
        return time() + (3 * 60 * 60);
    }

    private function getEnabledAt() {
        return mktime(0,0,0,12,20,2017) + (3 * 60 * 60);
    }

    private function getDisabledAt() {
        return mktime(0,0,0,1,11,2018) + (3 * 60 * 60);
    }

    public function enableDiscounts()
    {
        if ($this->getCurrentTime() >= $this->getEnabledAt()) {
            $this->updateDiscounts($this->sale_general_discount, $this->common_general_discount);
            $this->updateExceptions(15);
        }
    }

    public function disableDiscounts()
    {
            $this->updateDiscounts($this->common_general_discount, $this->sale_general_discount);
            $this->updateExceptions(10);
    }

    private function updateDiscounts($set, $where)
    {
        return Product::getDb()
            ->createCommand()
            ->update(
                Product::tableName(),
                ['discount_card_percent' => $set],
                'discount_card_percent = '.$where
            )
            ->execute();
    }

    private function updateExceptions($percent)
    {
        $products = [
            '10020201', // LANE
            '10008578', // Fins Training II Rubber
            '10020186', // POOL COLOUR SHORT
            '10014429', // BODYSHELL
            '10015972', // PRO SNORKEL
            '10005088', // Fins Training
            '10008870', // BODYSHELL JAMMER
            '10022584', // Forceshell 2017 Men Jammer
            '3790', // Flow
            '10020176', // POOL COLOUR LONG
            '10014815', // Turbo Racer II
            '10017864', // Carbshell Women full back
            '10017873', // Carbshell Women open back
            '10015773', // MAD TEAM
            '10022595', // Forceshell 2017 Women open back Racing Suit
        ];

        foreach ($products as $variant_id) {
            if ($v = ProductVariant::findOne($variant_id)) {
                $v->product->discount_card_percent = $percent;
                $v->product->save(false);
            };

        }
    }

    public function getLighingProduct()
    {
        $products = $this->getLighingProducts();

        foreach ($products as $pArr) {

            if ($this->getCurrentTime() >= $pArr['time'] && $this->getCurrentTime() <= strtotime('+1 day', $pArr['time'])) {
                $v = ProductVariant::findOne($pArr['sku']);
                return $v;
            }
        }
    }
}