<?php 

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\db\Migration;
use yii\db\Schema;

/**
 * Self Pick Up Points model
 *
 * Model for storing self pick-up points data
 *
 * @property int $id
 * @property string $title
 * @property string $city
 * @property string $metro_title
 * @property string $metro_color
 * @property string $address
 * @property string $working_hours
 * @property string $phone
 * @property string $is_pick_up_point
 * @property string $latitude
 * @property string $longitude
 * @property string $code
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class SelfPickUpPoints extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'self_pic_up_points';

        if (Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => Schema::TYPE_PK,
                'title' => Schema::TYPE_STRING . ' NOT NULL',
                'city' => Schema::TYPE_TEXT,
                'metro_title' => Schema::TYPE_TEXT,
                'metro_color' => Schema::TYPE_TEXT,
                'address' => Schema::TYPE_TEXT,
                'working_hours' => Schema::TYPE_TEXT,
                'phone' => Schema::TYPE_TEXT,
                'is_pick_up_point' => Schema::TYPE_TEXT,
                'latitude' => Schema::TYPE_TEXT,
                'longitude' => Schema::TYPE_TEXT,
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
            [[
                'id',
                'title',
                'city',
                'metro_title',
                'metro_color',
                'address',
                'working_hours',
                'phone',
                'is_pick_up_point',
                'latitude',
                'longitude',
            ], 'default'],
        ];
    }


}
