<?php

use common\models\showcase\ShowcaseItem;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $dataProvider \yii\data\ActiveDataProvider*/
?>

<h1 style="margin-bottom: 60px">
    Users
</h1>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'email',
        'rights',
        'created_at:datetime',
        [
            'format' => 'raw',
            'value' => function ($model) {
                /* @var $model \common\models\purchasing\Order */
                return
                    Html::a('update', Url::to(['users/view', 'id' => $model->getId()]))
                    ;
            }
        ]
    ],
]) ?>