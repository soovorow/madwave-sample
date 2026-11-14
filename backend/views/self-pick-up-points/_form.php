<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'city')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'metro_title')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'metro_color')->dropDownList([
        'red' => 'Красный',
        'blue' => 'Синий',
        'violet' => 'Фиолетовый',
        'green' => 'Зеленый',
        'orange' => 'Оранжевый',
    ], ['prompt' => 'None']) ?>
    <?= $form->field($model, 'address')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'working_hours')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'phone')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'is_pick_up_point')->checkbox() ?>
    <?= $form->field($model, 'latitude')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'longitude')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
