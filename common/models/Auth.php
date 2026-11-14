<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\db\cubrid\Schema;
use yii\db\Migration;

/**
 * Auth model
 *
 * Model for authentication via https://github.com/yiisoft/yii2-authclient
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Auth extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'auth';

        if (\Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => Schema::TYPE_PK,
                'user_id' => Schema::TYPE_INTEGER,
                'source' => Schema::TYPE_STRING,
                'source_id' => Schema::TYPE_STRING,
            ]);
        }

        return $table_name;

    }

    /**
     * Get User model
     *
     * @return User
     */
    public function getUser()
    {
        return User::findOne($this->user_id);
    }

}