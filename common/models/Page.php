<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\helpers\Markdown;
use yii\web\NotFoundHttpException;

/**
 * Class Page
 *
 * @property integer $id
 * @property string $title
 * @property string $meta_desc
 * @property string $slug
 * @property string $body
 *
 * @package common\models
 *
 * @author Vyacheslav Elchin <lexscite@gmail.com>
 */
class Page extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $tableName = '{{%page}}';

        if (!Yii::$app->db->getTableSchema($tableName)) {
            $migration = new Migration();
            $migration->createTable($tableName, [
                'id' => $migration->primaryKey(),
                'title' => $migration->string()->notNull(),
                'meta_desc' => $migration->string()->notNull(),
                'slug' => $migration->string()->notNull(),
                'body' => $migration->text()->notNull(),
            ]);
        }

        return $tableName;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'meta_desc', 'slug', 'body'], 'required'],
            [['title', 'meta_desc', 'slug', 'body'], 'string']
        ];
    }

    /**
     * Return
     *
     * @param $slug string Page slug
     * @return static
     * @throws NotFoundHttpException
     */
    public static function findBySlug($slug)
    {
        if (($model = self::findOne(['slug' => $slug])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * Converts body string to Html
     *
     * @return string Html to render
     */
    public function getBody()
    {
        return Markdown::process($this->body);
    }
}