<?php

namespace common\components\importer;

/**
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class History extends \yii\db\ActiveRecord
{

    public static function getDb()
    {
        return \Yii::$app->madwave_rpoducts;
    }
    public static function tableName()
    {
        return '__history';
    }

}
