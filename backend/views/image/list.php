<?php
/**
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $searchModel \backend\models\ImageSearch
 */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Картинки';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>
    <?= Html::encode($this->title) ?>
</h1>

<?php $form = ActiveForm::begin([
    'action' => ['list'],
    'method' => 'get',
]); ?>

<div class="row">
    <div class="col-sm-4">
        <?= $form->field($searchModel, 'key')->label('Поиск по заметкам'); ?>
    </div>
</div>

<div class="form-group">
    <?= Html::a('Создать', ['/image/create'], ['class' => 'btn btn-success']) ?>
    <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
    <?= Html::resetButton('Сбросить', ['class' => 'btn btn-default']) ?>
</div>

<?php ActiveForm::end(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'note',
//        'file_path',
        'url_path',
//        'created_at:datetime',
        [
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a('Редактировать', ['update' , 'id' => $model->id]).
                    ' '.
                    Html::a('Удалить', ['delete' , 'id' => $model->id]);
            }
        ],
    ],
]); ?>
