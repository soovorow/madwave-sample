<?php

namespace common\models\products;

/**
 * Currency model
 *
 * Model store product variant available sizes
 *
 *
 * @property mixed quote
 * @todo write @property
 * @todo write migration
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Currency extends \yii\db\ActiveRecord
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
        return 'currency';
    }

}
