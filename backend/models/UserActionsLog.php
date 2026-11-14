<?php

namespace backend\models;

use common\components\DateTimeFormatter;
use Nette\Utils\Json;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\db\Schema;

/**
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class UserActionsLog extends ActiveRecord
{

    const STATUS_HIDDEN = 0;
    const STATUS_POSTED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'user_action_log';

        if (\Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => Schema::TYPE_PK,
                'ip' => Schema::TYPE_STRING,
                'user_agent' => Schema::TYPE_STRING,
                'email' => Schema::TYPE_STRING,
                'action' => Schema::TYPE_STRING,
                'data' => Schema::TYPE_STRING,
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

    public static function log() {
        if ($_SERVER['REQUEST_URI'] !== '/admin/action-log/index'
            && $_SERVER['REQUEST_URI'] !== '/action-log/index'
            && $_SERVER['REQUEST_URI'] !== '/admin/action-log/index?sort=-id'
            && $_SERVER['REQUEST_URI'] !== '/action-log/index?sort=-id'
        ) {
            $l = new UserActionsLog([
                'ip' => $_SERVER['REMOTE_ADDR'],
                'action' => $_SERVER['REQUEST_URI'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'email' => \Yii::$app->user->isGuest ? 'GUEST' : \Yii::$app->user->identity->email,
                'data' => Json::encode(\Yii::$app->request->queryParams),
            ]);
            $l->save(false);
        }

    }

    public function getColumns() {
        return [
            'id',
            'email',
            'ip',
            'action',
            'data',
            'user_agent',
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return DateTimeFormatter::dateAsString($model->created_at);
                }
            ],
        ];
    }
}