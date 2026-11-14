<?php

namespace common\models;

use common\components\multilocal\Local;
use yii\db\ActiveRecord;


/**
 * Class Feature
 *
 * @property int $id
 * @property int $product_id
 * @property string $title
 * @property string description
 * @property int xpos
 * @property int ypos
 * @property mixed description_en
 * @property mixed title_en
 *
 * @package common\models
 *
 * @author Vyacheslav Elchin <lexscite@gmail.com>
 */
class Feature extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'easy_feature';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'xpos', 'ypos'], 'integer'],
            [['title', 'description'], 'string'],
            [['title_en', 'description_en'], 'string'],
            [['product_id', 'title', 'xpos', 'ypos'], 'required'],
        ];
    }

    /**
     * Get feature by it's product_id field
     *
     * @param $productId
     * @return array|Feature[]|ActiveRecord[]
     */
    public static function getByProductId($productId)
    {
        $features = self::find()
            ->where(['product_id' => $productId])
            ->all();

        return $features;
    }

    /**
     * Generates configuration array for EasyFeatures widget items
     *
     * @param $productId
     * @return array
     */
    public static function generateWidgetItems($productId)
    {
        $result = [];

        foreach(self::getByProductId($productId) as $feature)
        {
            $result[] = [
                'title' => $feature->getTitle(),
                'description' => $feature->getDescription(),
                'x' => $feature['xpos'],
                'y' => $feature['ypos'],
            ];
        }

        return $result;
    }

    public function getDescription()
    {
        return Local::choose([
            Local::RU => $this->description,
            Local::EU => $this->description_en
        ]);
    }

    public function getTitle()
    {
        return Local::choose([
            Local::RU => $this->title,
            Local::EU => $this->title_en
        ]);
    }
}