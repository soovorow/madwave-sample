<?php namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

class CRUDSearch extends ActiveRecord{

    public $keyword;

    public $model;

    public function __construct($model, array $config)
    {
        $this->model = new $model;
        parent::__construct($config);
    }

    public function search($params) {

        $model = $this->model;

        $query = $model::find()->orderBy('id DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($model->load($params) && $model->validate())) {
            return $dataProvider;
        }

        foreach ($model->attributes as $a) {
            $query->orFilterWhere(['like', $a, $this->keyword]);
        }

        return $dataProvider;
    }
}
