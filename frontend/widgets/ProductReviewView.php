<?php

namespace frontend\widgets;

use common\models\ReviewVote;
use yii\base\Widget;

/**
 * Product reviews widget
 * @author Dmitry Suvorov <soovorow@gmail.com>
 *
 */

class ProductReviewView extends Widget
{

    public $tt;
    public $model;
    public $v;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $vote = ReviewVote::find()
            ->where(['user_id' => (!\Yii::$app->user->isGuest ? \Yii::$app->user->identity->getId() : null )])
            ->andWhere(['review_id' => $this->tt->id])
            ->one();

        return $this->render('product_review_view', [
            'vote' => $vote,
            'tt' => $this->tt,
            'model' => $this->model,
            'v' => $this->v
        ]);
    }
}
