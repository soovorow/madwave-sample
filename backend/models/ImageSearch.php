<?php

namespace backend\models;

use common\models\content\Image;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ImageSearch extends Image
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
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Image::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->orFilterWhere([
            'note' => $this->key,
        ]);

        $query->orFilterWhere(['like', 'note', $this->key]);

        return $dataProvider;
    }
}