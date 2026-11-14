<?php
/**
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
?>
<h1>
    Collection
    <?= \yii\helpers\Html::a('Create', ['/collections/create'], ['class' => 'btn btn-primary pull-right']) ?>
</h1>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'title',
        'created_at:datetime',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}{delete}'
        ],
    ]
]) ?>