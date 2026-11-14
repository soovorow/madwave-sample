<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\cubrid\Schema;
use yii\db\Migration;

/**
 * Auth User Data model
 *
 * Model for storing user data from auth clients
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class AuthUserData extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'auth_user_data';

        if (Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => Schema::TYPE_PK,
                'auth_id' => Schema::TYPE_INTEGER,
                'email' => Schema::TYPE_STRING,
                'first_name' => Schema::TYPE_STRING,
                'last_name' => Schema::TYPE_STRING,
                'sex' => Schema::TYPE_STRING,
                'bdate' => Schema::TYPE_STRING,
            ]);
        }

        return $table_name;

    }

}