<?php

namespace frontend\modules\nanoclerk\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Item class
 *
 * @property integer id
 * @property string title
 * @property integer type
 * @property string html
 * @property string page
 * @property string created_at
 * @property string updated_at
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Item extends ActiveRecord
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
        return 'nc_item';
    }

}


