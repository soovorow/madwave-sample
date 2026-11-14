<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\db\Schema;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * BlogCategory model
 *
 * Categories for blog posts
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $title
 * @property string $slug
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class BlogCategory extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $table_name = 'blog_category';

        if (\Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => Schema::TYPE_PK,
                'parent_id' => Schema::TYPE_INTEGER,
                'title' => Schema::TYPE_STRING,
                'slug' => Schema::TYPE_STRING,
                'status' => Schema::TYPE_INTEGER,
                'created_at' => Schema::TYPE_INTEGER,
                'updated_at' => Schema::TYPE_INTEGER,
            ]);
        }

        return $table_name;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'title',
                ],
                'required'
            ],
            [
                [
                    'slug',
                    'parent_id',
                ],
                'default'
            ]
        ];
    }

    /**
     * Get attributes for form
     *
     * Get attributes for form in admin area implementing custom not completed crud inheritance
     *
     * @return array
     */
    public function formAttributes()
    {
        return [
            'parent_id' => ['type' => 'dropdown', 'items' => ArrayHelper::map(BlogCategory::find()->all(), 'id', 'title')],
            'title' => ['type' => 'textInput'],
            'slug' => ['type' => 'textInput'],
        ];
    }

    /**
     * Get columns for index
     *
     * Get attributes for index page in admin area implementing custom not completed crud inheritance
     *
     * @return array
     */
    public function getColumns()
    {
        return [
            'title',
            'created_at:datetime',
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a('update', ['update', 'id' => $model->id]);
                }
            ]
        ];
    }

    /**
     * Get parent category
     *
     * @return BlogCategory
     */
    public function getParent()
    {
        return BlogCategory::findOne(['id' => $this->parent_id]);
    }

    /**
     * Get URL
     *
     * @return string
     */
    public function getUrl()
    {
        return '/world/' . (!empty($this->parent->slug) ? $this->parent->slug . '/' : '') . $this->slug;
    }

}