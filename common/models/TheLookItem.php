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
 * @property int the_look_id
 * @property integer pos
 * @property mixed sku
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class TheLookItem extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'the_look_item';

        $table = \Yii::$app->db->schema->getTableSchema($table_name);

        if ($table === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => $migration->primaryKey(),
                'the_look_id' => $migration->integer(),
                'pos' => $migration->integer(),
                'sku' => $migration->string(),
                'index' => $migration->integer(),
                'created_at' => $migration->integer(),
                'updated_at' => $migration->integer(),
            ]);
        }

        if (!isset($table->columns['pos'])) {
            $migration = new Migration();
            $migration->addColumn($table_name, 'pos', $migration->integer()->after('the_look_id'));

            /**
             * @var TheLookItem $item
             */
            foreach (TheLookItem::find()->all() as $item) {
                $item->pos = $item->primaryKey;
                $item->save(false);
            }
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
            ['the_look_id','required'],
        ];
    }

}