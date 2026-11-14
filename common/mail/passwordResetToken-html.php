<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */
$resetLink = \Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <h1>
        Восстановление пароля на сайте Mad Wave
    </h1>

    <p>
        Перейдите по ссылке ниже чтобы восстановить ваш пароль:
    </p>

    <p>
        <?= Html::a(Html::encode($resetLink), $resetLink) ?>
    </p>

    <p>
        Свяжитесь с нами, в случае возникновения любых вопросов!
        С уважением, администрация madwave.ru!
    </p>
</div>