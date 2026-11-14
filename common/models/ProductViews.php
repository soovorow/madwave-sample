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
 * @property int product_id
 * @property int created_at
 *
 * @package common\models
 *
 * @author Vyacheslav Elchin <lexscite@gmail.com>
 */
class ProductViews extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $tableName = '{{%product_views}}';

        if (!Yii::$app->db->getTableSchema($tableName)) {
            $migration = new Migration();
            $migration->createTable($tableName, [
                'id' => $migration->primaryKey(),
                'product_id' => $migration->string()->notNull(),
                'created_at' => $migration->integer()->notNull(),
            ]);
        }

        return $tableName;
    }

}