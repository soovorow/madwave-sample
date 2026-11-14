<?php

namespace common\models;

use common\components\multilocal\Local;
use Yii;
use yii\db\ActiveRecord;
use yii\db\cubrid\Schema;
use yii\db\Migration;
use yii\helpers\Html;

/**
 * String model
 *
 *
 * @property string ru
 * @property string en
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class AppString extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'app_string';

        if (Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => Schema::TYPE_PK,
                'key' => Schema::TYPE_STRING,
                'en' => Schema::TYPE_TEXT,
                'ru' => Schema::TYPE_TEXT
            ]);
        }

        return $table_name;

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'key',
                ],
                'required'
            ],
            [
                [
                    'en',
                    'ru',
                ],
                'default'
            ],
        ];
    }

    public static function getString($key)
    {
        if ($string = AppString::findOne(['key' => $key])) {
            return Local::choose([
                Local::RU => $string->ru,
                Local::EU => $string->en
            ]);
        }

        return null;
    }

    public function getColumns() {
        return [
            'id',
            'key',
            'en',
            'ru',
            [
                'format' => 'raw',
                'value' => function($model) {
                    return
                        Html::a('update', ['update', 'id' => $model->id])
                        .' '. Html::a('delete', ['delete', 'id' => $model->id]);
                }
            ]
        ];
    }

    public function formAttributes() {
        return [
            'key' => ['type' => 'textInput'],
            'en' => ['type' => 'textInput'],
            'ru' => ['type' => 'textInput'],
        ];
    }

}