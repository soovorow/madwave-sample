<?php

use yii\bootstrap\ActiveForm;

/**
 * @var $model \frontend\models\A1
 */
?>

<section style="background: url('/images/layout/1.jpg') center center / cover no-repeat; padding-bottom: 71px; margin-bottom: -71px">
    <div class="container" style="padding-top: 60px">
        <div class="row">
            <div class="col-md-4 col-md-push-4" style="background: #fff; padding-top: 20px; padding-bottom: 20px">
                <p>
                    Добро пожаловать на страницу регистрации участников мероприятия A1 SWIM! Пожалуйста, заполните максимальное количество полей, чтобы мы могли лучше узнать друг друга.
                </p>

                <?php $form = ActiveForm::begin() ?>

                <?= $form->field($model, 'firstname') ?>
                <?= $form->field($model, 'lastname') ?>
                <?= $form->field($model, 'card') ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'phone') ?>
                <?= $form->field($model, 'sport') ?>
                <?= $form->field($model, 'rank') ?>

                <p>
                    *поля, помеченные звездочкой, являются обязательными для заполнения
                </p>
                <?= \yii\helpers\Html::submitButton('Зарегистрироваться', [
                    'class' => 'btn btn-primary'
                ]) ?>

                <?php $form::end() ?>
            </div>
        </div>
    </div>
</section>