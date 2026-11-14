<?php

namespace common\models\content;

use common\components\multilocal\Local;
use yii\db\ActiveRecord;
use yii\db\Migration;

class ProductLabel extends ActiveRecord
{
    public static function tableName()
    {
        $tableName = 'label';

        if (!\Yii::$app->db->getTableSchema($tableName))
        {
            $migration = new Migration();
            $migration->createTable($tableName, [
                'id' => $migration->primaryKey(),
                'product_id' => $migration->string(),
                'text' => $migration->string()->notNull(),
                'text_eu' => $migration->string()->notNull(),
                'hex' => $migration->string()->notNull(),
            ]);
        }

        return $tableName;
    }

    public function rules()
    {
        return [
            [['text'], 'string'],
            [['text_eu'], 'string'],
            [['hex'], 'string'],
        ];
    }

    public function formAttributes() {
        return [
            'text' => ['type' => 'textInput'],
            'text_eu' => ['type' => 'textInput'],
            'hex' => ['type' => 'textInput'],
        ];
    }

    public function getText()
    {
        return Local::choose([
            Local::RU => $this->text,
            Local::EU => $this->text_eu,
        ]);
    }
}