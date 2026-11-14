<?php

namespace frontend\modules\nanoclerk\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * User behavior class
 *
 * @property integer id
 * @property integer visitor_id
 * @property integer item_id
 * @property integer action
 * @property string created_at
 * @property string updated_at
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Behavior extends ActiveRecord
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
        return 'nc_visitor_interest';
    }

}


