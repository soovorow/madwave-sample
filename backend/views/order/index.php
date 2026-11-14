<?php

use common\components\multilocal\Local;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1>
        <?= Html::encode($this->title) ?>

        <?= Html::a('Create Order', ['create'], ['class' => 'btn btn-default pull-right']) ?>
        <?= Html::a('Yesterday report ', ['report'], ['class' => 'btn btn-success pull-right', 'style' => 'margin-right: 20px']) ?>
    </h1>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'value' => function ($model) {
                    return $model->getId();

                }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function ($model) {
                    $today = date('d.m.Y');
                    $yesterday = date('d.m.Y', strtotime('-1 day'));

                    $order = date('d.m.Y', $model->created_at);
                    if ($today == $order) {
                        $orderDate = 'Сегодня,';
                    } elseif ($yesterday == $order) {
                        $orderDate = 'Вчера,';
                    } else {
                        $orderDate = $order;
                    }

                    return $orderDate.' <b>'.date('H:i', $model->created_at).'</b>';

                }
            ],
            [
                'label' => 'Телефон',
                'attribute' => 'customer_phone',
                'value' => function ($model) {
                    return $model->customer_phone;

                }
            ],
            [
                'label' => 'E-mail',
                'attribute' => 'customer_email',
                'format' => 'raw',
                'value' => function ($model) {
                    return '<a href="mailto:'.$model->customer_email.'">'.$model->customer_email.'</a>' ;

                }
            ],
            [
                'attribute' => 'total',
                'format' => 'raw',
                'value' => function ($model) {
                    return number_format($model->total, 2, '.', ',') .' '.($model->website == Local::EU ? 'eur.' : 'руб.') ;

                }
            ],
            'track_code',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    /** @var $model \common\models\Order */
                    return $model::getOrderStatuses()[$model->status];

                }
            ],
            [
                'label' => 'ФИО',
                'attribute' => 'customer_lastname',
                'value' => function ($model) {
                    /** @var $model \common\models\Order */
                    return $model->getCustomerFullName();
                }
            ],
            'customer_notes',
            [
                'attribute' => 'website',
                'filter'=> [
                    Local::RU => 'Russia',
                    Local::EU => 'Europe',
                ],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}'
            ],
        ],
    ]); ?>

</div>
