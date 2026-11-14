<?php

namespace frontend\models;

use common\models\BlogPost;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class BlogPostSearch extends BlogPost
{
    public $keywords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['keywords'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
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
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BlogPost::find()->where(['status' => 1])->orderBy('id DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query
            ->orFilterWhere(['like', 'title', $this->keywords])
            ->orFilterWhere(['like', 'content', $this->keywords])
            ->orFilterWhere(['like', 'preview', $this->keywords])
        ;

        return $dataProvider;
    }
}
