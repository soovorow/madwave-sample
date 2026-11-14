<?php

namespace common\models;

use common\components\multilocal\Local;
use Yii;
use yii\db\ActiveRecord;
use yii\db\cubrid\Schema;
use yii\db\Migration;

/**
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class OutOfStock extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'out_of_stock';

        if (Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => Schema::TYPE_PK,
                'date' => Schema::TYPE_STRING,
                'vendor_code' => Schema::TYPE_STRING
            ]);
        }

        return $table_name;

    }

    public static function run()
    {
        $date = time();

        $query = ProductVariant::find()
            ->innerJoin('price', 'price.variant_id = product_variant.variant_id')
            ->andWhere(['>', 'price.price_rub', 0])
            ->rightJoin('variant_stock vs', 'vs.variant_id = product_variant.variant_id')
            ->andWhere(['vs.store_id' => Local::choose([
                Local::RU => '00005',
                Local::EU => '00049',
            ])])
            ->andWhere(['vs.quantity' => 0])
        ;

        $data = $query->asArray()->all();

        foreach ($data as $p) {
            $o = new OutOfStock();
            $o->date = $date;
            $o->vendor_code = $p['vendore_code'];
            $o->save(false);
        }

        echo "DONE \n";
    }


}