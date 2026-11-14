<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Product;

/**
 * ProductSearch represents the model behind the search form about `common\models\Product`.
 */
class ProductSearch extends Product
{

    public $key;

    public function rules()
    {
        return [
            ['key', 'safe']
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @param bool $query_only
     * @return ActiveDataProvider
     */
    public function search($params, $query_only = false)
    {
        $query = Product::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->orFilterWhere([
            'product_id' => $this->key,
//            'category_id' => $this->category_id,
        ]);

        $query->orFilterWhere(['like', 'slug', $this->key]);
        $query->orFilterWhere(['like', 'name', $this->key]);

        if ($query_only) {
            return $query;
        }

        return $dataProvider;
    }
}
