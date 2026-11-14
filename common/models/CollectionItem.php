<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Migration;

/**
 * Collection model
 *
 * @property integer $id
 * @property int collection_id
 * @property mixed sku
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class CollectionItem extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'collection_item';

        if (\Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => $migration->primaryKey(),
                'collection_id' => $migration->integer(),
                'sku' => $migration->string(),
                'index' => $migration->integer(),
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
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['sku','required'],
            ['collection_id','required'],
        ];
    }

}