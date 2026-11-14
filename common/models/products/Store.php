<?php

namespace common\models\products;

/**
 * Stores model
 *
 * Model store product variant available sizes
 *
 * @todo write @property
 * @todo write migration
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Store extends \yii\db\ActiveRecord
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
        return 'store';
    }

}
