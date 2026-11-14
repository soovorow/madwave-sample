<?php

namespace frontend\modules\nanoclerk\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * User behavior identity class
 *
 * @property integer id
 * @property string email
 * @property string session
 * @property string ga
 * @property string created_at
 * @property string updated_at
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Visitor extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [ TimestampBehavior::className() ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nc_visitor';
    }


}


