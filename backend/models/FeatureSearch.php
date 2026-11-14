<?php

namespace backend\models;

use common\models\Feature;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class FeatureSearch is the search model for Feature model
 *
 * @package backend\models
 *
 * @author Vyacheslav Elchin <lexscite@gmail.com>
 */
class FeatureSearch extends Feature
{
    public $key;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['key', 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Returns data provider with query to get features specified by search form
     *
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Feature::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->orFilterWhere([
            'product_id' => $this->key,
        ]);

        $query->orFilterWhere(['like', 'product_id', $this->key]);

        return $dataProvider;
    }
}