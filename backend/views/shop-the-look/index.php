<?php

use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var $dataProvider \yii\data\ActiveDataProvider
 */

?>

<h1>
    The Looks
    <?= Html::a('Create', ['/shop-the-look/create'], ['class' => 'btn btn-primary pull-right']) ?>
</h1>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        [
            'attribute' => 'weight',
            'format' => 'raw',
            'value' => function ($model) use ($dataProvider) {

                /**
                 * @var $model \common\models\TheLook
                 */

                $upper = null;
                $downer = null;

                $key = array_search($model, $dataProvider->models, true);

                if (isset($dataProvider->models[$key - 1])) {
                    $upper = $dataProvider->models[$key - 1];
                }

                if (isset($dataProvider->models[$key + 1])) {
                    $downer = isset($dataProvider->models[$key + 1]);
                }

                $result = [];

                if ($upper) {
                    $result[] = Html::a('Up', ['weight', 'first_id' => $model->id, 'second_id' => $upper->id]);
                }

                if ($downer) {
                    $result[] = Html::a('Down', ['weight', 'first_id' => $model->id, 'second_id' => $downer->id]);
                }

                return implode($result, ' ');
            }
        ],
        'title',
        [
            'attribute' => 'status',
            'value' => function ($model) {
                return $model->status == 0 ? 'Draft' : 'Published';
            }
        ],
        'created_at:datetime',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}{delete}'
        ],
    ]
]) ?>