<?php

namespace common\models\products;

/**
 * Sizes model
 *
 * Model store product variant available sizes
 *
 * @property string name
 * @todo write @property
 * @todo write migration
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Size extends \yii\db\ActiveRecord
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
        return 'size';
    }
}
