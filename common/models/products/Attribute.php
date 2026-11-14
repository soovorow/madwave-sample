<?php

namespace common\models\products;

/** 
 * Attribute model 
 * 
 * Attribute data imported from 1c
 *
 * @todo write @property
 * @todo write migration
 * 
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Attribute extends \yii\db\ActiveRecord
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
        return 'attribute';
    }

}
