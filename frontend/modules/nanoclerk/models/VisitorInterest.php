<?php

namespace frontend\modules\nanoclerk\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\db\Schema;

/**
 * Visitor to interest m2m class
 *
 * @property integer id
 * @property integer visitor_id
 * @property integer interest_id
 * @property string created_at
 * @property string updated_at
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class VisitorInterest extends ActiveRecord
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


