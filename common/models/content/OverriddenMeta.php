<?php

namespace common\models\content;

use common\components\multilocal\Local;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\db\Schema;
use yii\helpers\Html;
use yii\web\UploadedFile;

/**
 * @param string $meta_title_ru
 * @param string $meta_description_ru
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class OverriddenMeta extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $table_name = 'overriden_meta';

        if (\Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => $migration->primaryKey(),
                'product_id' => $migration->integer(),
                'meta_title_ru' => $migration->string(),
                'meta_title_en' => $migration->string(),
                'meta_description_ru' => $migration->string(),
                'meta_description_en' => $migration->string(),
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
                    'product_id',
                    'meta_title_ru',
                    'meta_description_ru',
                ],
                'required'
            ],
            [
                [
                    'meta_title_en',
                    'meta_description_en',
                ],
                'default'
            ],
        ];
    }

    public function getColumns() {
        return [
            'id',
            'meta_title_ru',
            'meta_description_ru',
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
            'meta_title_ru' => ['type' => 'textInput'],
            'meta_description_ru' => ['type' => 'textInput'],
            'product_id' => ['type' => 'textInput'],
        ];
    }
}

