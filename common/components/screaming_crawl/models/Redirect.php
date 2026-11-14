<?php

namespace common\components\screaming_crawl\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\db\Schema;
use yii\helpers\Html;

/**
 * @property string $from
 * @property string $to
 * @property string $why
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Redirect extends ActiveRecord
{
    public static function tableName()
    {
        $table_name = 'redirect';
        if (Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id'                         => Schema::TYPE_PK,
                'from'                    => Schema::TYPE_TEXT,
                'to'                    => Schema::TYPE_TEXT,
                'why'                    => Schema::TYPE_TEXT,
            ]);
        }
        return $table_name;
    }

    public function rules()
    {
        return [
            [['from', 'to'], 'required'],
            [['why'], 'default']
        ];
    }

    public function getColumns()
    {
        return [
            'id',
            'from',
            'to',
            'why',
            [
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a('update', ['update', 'id' => $model->id]);
                }
            ]
        ];
    }

    public function formAttributes()
    {
        return [
            'from'            => ['type' => 'textInput'],
            'to'                 => ['type' => 'textInput'],
            'why'                 => ['type' => 'textInput'],
        ];
    }
}