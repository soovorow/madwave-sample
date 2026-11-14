<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('app', 'Log in').' | Mad Wave';

$this->params['breadcrumbs'][] = Yii::t('app', 'Log in');

?>
<div class="container">
    <div class="site-login">
        <h1 class="text-center"><?= Yii::t('app', 'Log in') ?></h1>

        <hr>

        <div class="row">
            <div class="col-xs-12 col-md-4 col-md-push-4">

                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Log in'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
                <?php ActiveForm::end(); ?>

                <div class="pull-right" style="margin-top: -50px">
                    <?= yii\authclient\widgets\AuthChoice::widget([
                        'baseAuthUrl' => ['site/auth'],
                        'popupMode' => true,
                    ]) ?>
                </div>

                <div style="color:#999;margin:1em 0">
                    <?= Html::a(Yii::t('app', 'Forget your password?'), ['site/request-password-reset']) ?>
                </div>
                <?= Html::a(Yii::t('app', 'Sign up'), \yii\helpers\Url::to(['site/signup'], ['class' => 'btn btn-primary'])) ?>
            </div>
        </div>

    </div>

</div>