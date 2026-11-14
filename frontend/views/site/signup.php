<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'Регистрация | Mad Wave';
$this->params['breadcrumbs'][] = 'Регистрация';
?>
<div class="container">
    <div class="site-signup">
        <h1 class="text-center">Регистрация</h1>

        <hr>

        <div class="row">
            <div class="col-xs-12 col-md-4 col-md-push-4">
                <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <div class="form-group">
                    <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>
                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
