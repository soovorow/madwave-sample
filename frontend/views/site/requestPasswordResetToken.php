<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

$title = 'Сброс пароля';
$this->title = $title.' | Mad Wave';
$this->params['breadcrumbs'][] = ['label' => 'Вход в личный кабинет', 'url' => '/login'];
$this->params['breadcrumbs'][] = $title;
?>
<div class="container">
    <div class="site-request-password-reset">
        <h1><?= $title ?></h1>

        <p>Пожалуйста, введите свой e-mail адрес. Ссылка для сброса пароля будет отправлена вам на почтовый ящик.</p>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
                <?= $form->field($model, 'email') ?>
                <div class="form-group">
                    <?= Html::submitButton('Сбросить пароль', ['class' => 'btn btn-primary']) ?>
                    <a href="<?= \yii\helpers\Url::to(['site/signup']) ?>" class="btn btn-default" style="margin-left: 30px">Зарегистрироваться</a>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
