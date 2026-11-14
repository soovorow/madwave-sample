<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\db\Migration;
use yii\db\Schema;

/**
 * User Data model
 *
 * Model for storing user information data
 *
 * @property int $id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $second_name
 * @property string $phone
 * @property string $client_card
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class UserData extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'user_data';

        if (Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => Schema::TYPE_PK,
                'user_id' => Schema::TYPE_STRING . ' NOT NULL',
                'first_name' => Schema::TYPE_STRING,
                'last_name' => Schema::TYPE_STRING,
                'second_name' => Schema::TYPE_STRING,
                'phone' => Schema::TYPE_STRING,
                'city' => Schema::TYPE_STRING,
                'client_card' => Schema::TYPE_STRING,
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
                    'first_name',
                    'last_name',
                    'second_name',
                    'phone',
                ], 'default'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'second_name' => 'Отчество',
            'phone' => 'Мобильный телефон',
        ];
    }

    /**
     * Set client card
     */
    public function setClientCard($card) {
        $this->client_card = $card;
        return $this->save(false);
    }
}
