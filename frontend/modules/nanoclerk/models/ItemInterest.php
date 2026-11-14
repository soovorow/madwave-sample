<?php

namespace frontend\modules\nanoclerk\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Item to interest m2m class
 *
 * @property integer id
 * @property integer item_id
 * @property integer interest_id
 * @property string created_at
 * @property string updated_at
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class ItemInterest extends ActiveRecord
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
        return 'nc_item_interest';
    }

}


