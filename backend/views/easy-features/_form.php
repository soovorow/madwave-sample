<?php

/* @var $this yii\web\View */
/* @var $model common\models\Feature */
/* @var $form yii\widgets\ActiveForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="feature-form">

    <div class="col-xs-6">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'product_id')->textInput() ?>
        <div class="row">
            <div class="col-xs-6"><?= $form->field($model, 'title')->textInput() ?></div>
            <div class="col-xs-6"><?= $form->field($model, 'title_en')->textInput() ?></div>
        </div>
        <div class="row">
            <div class="col-xs-6"><?= $form->field($model, 'description')->textInput() ?></div>
            <div class="col-xs-6"><?= $form->field($model, 'description_en')->textInput() ?></div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'xpos')->textInput() ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'ypos')->textInput() ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
