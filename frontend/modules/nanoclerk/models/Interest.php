<?php

namespace frontend\modules\nanoclerk\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Interest class
 *
 * @property integer id
 * @property string title
 * @property string created_at
 * @property string updated_at
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Interest extends ActiveRecord
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
        return 'nc_interest';
    }

}


