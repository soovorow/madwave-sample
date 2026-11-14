<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="row">
    <div class="col-sm-6">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($image, 'imageFile')->fileInput(); ?>
        <?= $form->field($image, 'note'); ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
