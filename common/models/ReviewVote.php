<?php namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\db\Schema;

class ReviewVote extends ActiveRecord
{
    public static function tableName()
    {
        $table_name = 'review_votes';
        if (Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => Schema::TYPE_PK,
                'user_id' => Schema::TYPE_INTEGER,
                'review_id' => Schema::TYPE_INTEGER,
                'value' => Schema::TYPE_INTEGER,
            ]);
        }
        return $table_name;
    }
}