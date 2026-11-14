<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Migration;

/**
 * A1 event form
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class A1 extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'a1';

        if (Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => $migration->primaryKey(),
                'firstname' => $migration->string(),
                'lastname' => $migration->string(),
                'card' => $migration->string(),
                'email' => $migration->string(),
                'phone' => $migration->string(),
                'sport' => $migration->string(),
                'rank' => $migration->string(),
                'created_at' => $migration->integer(),
                'updated_at' => $migration->integer(),
            ]);
        }

        return $table_name;

    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [TimestampBehavior::className()];
    }

    public function rules()
    {
        return [
            [
                [
                    'firstname',
                    'lastname',
                    'card',
                    'email',
                    'phone',
                ],
                'required'
            ],
            ['email', 'email'],
            [
                [
                    'sport',
                    'rank',
                ],
                'default'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'firstname' => 'Имя*',
            'lastname' => 'Фамилия*',
            'card' => 'Номер карты Mad Wave*',
            'email' => 'E-mail*',
            'phone' => 'Контактный телефон',
            'sport' => 'Вид спорта (если заниматетесь спортом)',
            'rank' => 'Разряд (если есть)',
        ];
    }

}