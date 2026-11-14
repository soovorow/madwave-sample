<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\db\Schema;

/**
 * Order Metrics model
 *
 * model for storing ordering data form google analytics
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class OrderMetrics extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return \Yii::$app->madwave_rpoducts;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'product_order_metrics';

        if (Yii::$app->madwave_rpoducts->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->db = Yii::$app->madwave_rpoducts;
            $migration->createTable($table_name, [
                'product_id' => Schema::TYPE_INTEGER,
                'ctr' => Schema::TYPE_DOUBLE,
            ]);
        }

        return $table_name;
    }

}