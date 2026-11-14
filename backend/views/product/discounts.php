<?php

use yii\helpers\Html;


$this->title = 'Available for discounts';
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
?>

<? $form = \yii\widgets\ActiveForm::begin([
    'options' => [
        'enctype' => 'multipart/form-data'
    ]
]) ?>





<h1><?= Html::a($model->name, $model->url) ?> <?= Html::encode($this->title) ?> </h1>

<br>

<label>Уникальная скидка по карте <br> <?= Html::input('number', 'discount_card_percent', $model->discount_card_percent, ['class' =>'form-control']) ?> </label>

<br>

<label><?= Html::checkbox('discount_card', $model->discount_card) ?> Запретить скидку по карте</label>

<br>

<label><?= Html::checkbox('discount_code', $model->discount_code) ?> Запретить скидку купонам</label>

<br>

<br>

<?= Html::submitButton('save', ['class' => 'btn btn-success']) ?>

<? $form::end() ?>
