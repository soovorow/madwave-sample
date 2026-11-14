<?php

namespace common\models\products;

/**
 * FobRegion model
 *
 * @todo write @property
 * @todo write migration
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class FobRegion extends \yii\db\ActiveRecord
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
        return 'fob_region';
    }

}
