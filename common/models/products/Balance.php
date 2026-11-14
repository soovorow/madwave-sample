<?php

namespace common\models\products;

/**
 * Balance model
 *
 * Model store stock quantity for product variants
 *
 * @property integer quantity
 *
 * @property int holded_at
 *
 * @todo write @property
 * @todo write migration
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Balance extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return \Yii::$app->madwave_rpoducts;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'variant_stock';
    }

    public function reduce($quantity)
    {
        $this->quantity -= $quantity;
        $this->holded_at = time();
        return $this->save(false);
    }

}
