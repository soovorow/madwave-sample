<?php

/* @var $this \common\models\Page */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="page-index">
    <h1>
        Pages
        <span>
        <a href="<?= Url::to(['/page/create']) ?>" class="btn btn-success pull-right">Create</a>
        </span>
    </h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'title',
            'meta_desc',
            'slug',
            'body',
            [
                'label' => 'Actions',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a('Edit', ['/page/edit', 'id' => $model->id]).
                        ' '.
                        Html::a('Remove', ['/page/delete', 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>
</div>
