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
 * @property string $in_pick_up_point
 * @property string $latitude
 * @property string $longitude
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class RelatedProducts extends \yii\db\ActiveRecord
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
        $table_name = 'related_products';

        if (Yii::$app->madwave_rpoducts->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->db = Yii::$app->madwave_rpoducts;
            $migration->createTable($table_name, [
                'product_id' => Schema::TYPE_INTEGER,
                'related_product_id' => Schema::TYPE_INTEGER,
                'correlation_score' => Schema::TYPE_DOUBLE,
            ], 'ENGINE=InnoDB');
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
                'product_id',
                'related_product_id',
                'correlation_score',
            ], 'default'],
        ];
    }

    /**
     * Wired function for gathering data
     *
     * @todo i think it is useless and i need to delete it
     * @return void
     */
    public static function re()
    {
        $gaApiUrl = 'https://www.googleapis.com/analytics/v3/data/ga?ids=ga%3A123550906&start-date=30daysAgo&end-date=yesterday&metrics=ga%3AcorrelationScore&dimensions=ga%3AqueryProductId%2Cga%3ArelatedProductId&max-results=3000&access_token=ya29.CjA0A9NthY_2XEajEQzE5gpOzxGIg_ODVsKyCzMBfXe9zGUISZROpzaJ0eneKndR_r4';

        $rows = json_decode(file_get_contents($gaApiUrl))->rows;

        foreach ($rows as $row) {
            $r = new self;
            $r->product_id = ProductVariant::find()->where(['like', 'vendore_code', $row[0]])->limit(1)->all()[0]->product->product_id;;
            $r->related_product_id = ProductVariant::find()->where(['like', 'vendore_code', $row[1]])->limit(1)->all()[0]->product->product_id;;
            $r->correlation_score = $row[2];
            try {
                $r->save(false);
            } catch (Exception $e) {

            }
        }

    }
}
