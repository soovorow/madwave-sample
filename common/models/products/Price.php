<?php

namespace common\models\products;

use common\components\multilocal\Local;

/**
 * Price model for table from 1c import
 *
 * @property int $variant_id
 * @property double $price_rub
 * @property double $price_rub_choky
 * @property double $price_rub_sale
 * @property double $price_rub_wh
 * @property double $price_eur_wh
 * @property double $price_eur_rrp
 * @property double $price_usd_wh
 * @property double $price_usd_rrp
 * @property double $price_usd_purchasing
 * @property double $price_usd_preorder
 * @property double $price_eur_preorder
 * @property double $price_usd_vendor
 * @property double $price_usd_int_wh
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */

class Price extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function getDb() {
        return \Yii::$app->madwave_rpoducts;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'price';
    }

    public function getPurchasingPrice($in_rub = false)
    {
        if ($in_rub) {
            /** @var $currency Currency */
            $currency = Currency::findOne(['code' => 'USD']);
            return $this->price_usd_purchasing * $currency->quote;
        }
        return $this->price_usd_purchasing;
    }

    /**
     * Get price column for current app
     *
     * @return string
     */
    public static function getPriceColumn() {
        return Local::choose([
            Local::RU => 'price_rub',
            Local::EU => 'price_eur_rrp'
        ]);
    }
}
