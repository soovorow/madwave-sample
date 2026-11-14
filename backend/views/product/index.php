<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1>
        <?= Html::encode($this->title) ?>
    </h1>

    <?= $this->render('_search', ['model' => $searchModel]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            'id',
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model) {
                    return '<a href="'.$model->url.'">'.$model->name.'</a>';
                }
            ],
            'slug',
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a('Каталог', ['view-params' , 'id' => $model->product_id] ,[]);
                }
            ],
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a('Скидки', ['discounts' , 'id' => $model->product_id] ,[]);
                }
            ],
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a('Настройки', ['update' , 'id' => $model->product_id] ,[]);
                }
            ],
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a('Meta', ['overridden-meta/update' , 'product_id' => $model->product_id] ,[]);
                }
            ],
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a('Label', ['product-label/update' , 'product_id' => $model->product_id] ,[]);
                }
            ],

        ],
    ]); ?>

</div>
