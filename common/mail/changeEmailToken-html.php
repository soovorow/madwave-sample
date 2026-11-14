<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */
$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/confirm-email-change', 'token' => $user->email_change_token]);
?>

<div class="password-reset">
    <h1>
        Изменение e-mail адреса на сайте Mad Wave
    </h1>

    <p>
        Перейдите по ссылке ниже чтобы подтвердить изменение почтового ящика:
    </p>

    <p>
        <?= Html::a(Html::encode($resetLink), $resetLink) ?>
    </p>

    <p>
        Свяжитесь с нами, в случае возникновения любых вопросов!
        С уважением, администрация madwave.ru!
    </p>
</div>