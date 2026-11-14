<?php
namespace common\models\content;

use common\models\Product;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Migration;

/**
 * @property integer $id
 * @property int banner_id
 * @property int product_id
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class TopBannerProducts extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'product_top_banner_products';

        if (\Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => $migration->primaryKey(),
                'banner_id' => $migration->integer(),
                'product_id' => $migration->integer(),
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

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['product_id' => 'product_id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['product_id','required'],
            ['banner_id','required'],
        ];
    }

}