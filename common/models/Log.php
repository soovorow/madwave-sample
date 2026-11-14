<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\cubrid\Schema;
use yii\db\Migration;

/**
 * Log model
 *
 * Model for logging and debugging data which we can see at admin panel
 *
 * @property int $id
 * @property string $message
 * @property string $type
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Log extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'log';

        if (Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => Schema::TYPE_PK,
                'message' => Schema::TYPE_STRING,
                'type' => Schema::TYPE_STRING
            ]);
        }

        return $table_name;

    }

    /**
     * Log::it()
     *
     * Main static method for logging data
     *
     * @param string $status just key for faster way to find logged data
     * @param string $message
     * @return void
     */
    public static function it($status, $message)
    {
        $log = new Log();
        $log->type = $status;
        $log->message = $message;
        $log->save();
    }

}