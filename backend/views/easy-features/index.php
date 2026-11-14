<?php

/* @var $this yii\web\View */
/* @var $searchModel backend\models\FeatureSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'EasyFeatures';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="easyfeatures-index">
    <h1>
        <?= Html::encode($this->title) ?>
        <?= Html::a('Create New', ['/easy-features/create'], ['class' => 'btn btn-success']) ?>
    </h1>

    <?= $this->render('_search', ['model' => $searchModel]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'product_id',
            'title',
            'description',
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a('update', ['update' , 'id' => $model->id] ,[]);
                }
            ],
        ],
    ]); ?>
</div>
