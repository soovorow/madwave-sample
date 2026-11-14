<?php

namespace common\models\products;
use common\components\multilocal\Local;

/**
 * Color model
 *
 * Model store product variant available colors
 *
 * @property mixed name_ru
 * @property mixed name_en
 * @property mixed color_id
 * @property string name
 * @todo write migration
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Color extends \yii\db\ActiveRecord
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
        return 'color';
    }

    public function getName() {
        return Local::choose([
            Local::RU => $this->name_ru,
            Local::EU => $this->name_en,
        ]);
    }

}
