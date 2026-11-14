<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ProductSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div style="margin-top: -45px; margin-bottom: 30px">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="col-md-7"></div>
    <div class="col-md-4"><?= $form->field($model, 'keyword')->textInput(['placeholder' => 'Search...'])->label(false) ?></div>
    <div class="col-md-1"> <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?></div>
    <?php ActiveForm::end(); ?>
</div>


