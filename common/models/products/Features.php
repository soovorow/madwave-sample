<?php

namespace common\models\products;

use common\models\Product;
use common\models\Settings;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * Features model
 *
 * @property int $feature_id
 * @property int $product_id
 * @property string img
 * @property string title
 * @property string description
 * @property string content
 * @property string title_ru
 * @property string description_ru
 * @property string $content_ru
 * @property string $weight
 * @property mixed $image
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */

class Features extends \yii\db\ActiveRecord {

    public $image;

    /**
     * @inheritdoc
     */
    public static function getDb() {
        return \Yii::$app->madwave_rpoducts;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'product_feature';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [
                [
                    'img',
                    'title',
                    'description',
                    'content',
                    'title_ru',
                    'description_ru',
                    'content_ru',
                    'weight',
                    'status',
                ],
                'default']
            ,

            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * Render feature <img>
     *
     * @return string
     */
    public function renderCover() {
        return Html::img(self::getCoverUrl(), ['width'=> '100%']);
    }

    /**
     * Url of feature <img>
     *
     * @return string
     */
    public function getCoverUrl() {
        return Yii::$app->params['baseUrl'].'/images/features/'.Json::decode($this->img);
    }

    /**
     * Getter for feature's Product
     *
     * @return array|\yii\db\ActiveRecord
     */
    public function getProduct() {
        return Product::find()->where(['product_id' => $this->product_id])->one();
    }

}
