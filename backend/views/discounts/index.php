<?php

use frontend\modules\checkout\ru\models\pricing\PromoCode;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Discounts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1>
        Promo Codes
        <?= Html::a('Create Promo Code', ['create'], ['class' => 'btn btn-success pull-right']) ?>
    </h1>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'code'          ,
            'title'          ,
            [
                'attribute' => 'type',
                'value' => function($model, $key, $index, $widget) {
                    return PromoCode::types()[$model->type];
                }
            ],
            'value'         ,
            [
                'attribute' => 'allow_with_card',
                'label' => 'CC',
                'value' => function($model, $key, $index, $widget) {
                    return $model->allow_with_card == 0 ? 'Нет' : 'Да';
                }
            ],
            'excluding_type',
            [
                'attribute' => 'group',
                'label' => 'Только для',
                'format' => 'raw',
                'value' => function($model, $key, $index, $widget) {
                    $category = \common\models\LocalCategory::findOne($model->group);
                    if ($category) {
                        return Html::a($category->title, \yii\helpers\Url::to(['local-category/update', 'id' => $category->id]));
                    }
                    return null;
                }
            ],
            [
                'attribute' => 'n_usage',
                'label' => 'N'
            ],
            'was_used'      ,
            [
                'class' => 'yii\grid\ActionColumn'
            ],
        ],
    ]); ?>


    <h1>Update card discounts</h1>
    <div class="row">
        <div class="col-xs-6">
            <?php $form = \yii\bootstrap\ActiveForm::begin() ?>
            <?= Html::textarea('set-discounts', null, ['class' => 'form-control']) ?>
            <?= Html::submitButton('save', ['class' => 'btn btn-default']) ?>
            <?php $form::end() ?>
        </div>
    </div>

</div>
