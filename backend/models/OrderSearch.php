<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Order;

/**
 * OrderSearch represents the model behind the search form about `common\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['customer_lastname'], 'string'],
            [['website'], 'string'],
            [['customer_phone'], 'string'],
            [['customer_email'], 'string'],
        ];
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['website' => $this->website]);

        $query->orFilterWhere(['id' => $this->id]);

        $query->orFilterWhere(['like', 'customer_phone', $this->customer_phone]);
        $query->orFilterWhere(['like', 'customer_email', $this->customer_email]);

        $query
            ->orFilterWhere(['like', 'customer_firstname', $this->customer_lastname])
            ->orFilterWhere(['like', 'customer_lastname', $this->customer_lastname])
            ->orFilterWhere(['like', 'customer_secondname', $this->customer_lastname])
        ;

        return $dataProvider;
    }
}
