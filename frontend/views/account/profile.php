<?php

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Account'), 'url' => \yii\helpers\Url::to(['account/orders'])];

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile')];

?>

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-3">
            <?= $this->render('_menu') ?>
        </div>
        <div class="col-xs-12 col-md-9">

            <h1 class="mt0 mb30"><?= Yii::t('app', 'Profile') ?> <span class="btn btn-default btn-xs edit-user-data <?=
                empty($user_data->last_name)
                || empty($user_data->first_name)
                || empty($user_data->second_name)
                || empty($user_data->phone)
                    ? 'hidden'
                    : ''
                ?>">
                    Изменить
                </span>
            </h1>
            <div class="row mb60">
                <div class="col-md-6">
                    <? $form = \yii\widgets\ActiveForm::begin([
                        'options' => ['id' => 'user-data']
                    ]) ?>
                    <?= $form->field($user_data, 'last_name')->textInput([
                        'disabled' => empty($user_data->last_name) ? false : true
                    ]) ?>
                    <?= $form->field($user_data, 'first_name')->textInput([
                        'disabled' => empty($user_data->first_name) ? false : true
                    ]) ?>
                    <?= $form->field($user_data, 'second_name')->textInput([
                        'disabled' => empty($user_data->second_name) ? false : true
                    ]) ?>
                    <?= $form->field($user_data, 'phone')->textInput([
                        'disabled' => empty($user_data->phone) ? false : true
                    ]) ?>
                    <?= \yii\helpers\Html::submitInput('Сохранить', [
                        'value' => 'save-proile',
                        'class' => 'btn btn-success save-user-data pull-right '
                            . (
                                empty($user_data->last_name)
                                || empty($user_data->first_name)
                                || empty($user_data->second_name)
                                || empty($user_data->phone)
                                    ? ''
                                    : 'hidden'
                            )
                    ]) ?>
                    <? $form::end() ?>
                </div>
            </div>

            <?php

            yii\authclient\widgets\AuthChoiceAsset::register($this);

            $auth = \common\models\Auth::findAll(['user_id' => Yii::$app->user->identity->getId()]);
            $sources = [];

            foreach ($auth as $a) {
                $sources[] = $a->source;
            }

            if (count($sources) > 0) { ?>

                <h1 class="mb30">Привязать аккаунт в соц-сетях</h1>
                <div class="row mb60">
                    <div class="col-md-6">
                        <ul class="auth-clients">

                            <?php if (!in_array('vkontakte', $sources)) { ?>
                                <li><a class="vkontakte auth-link" href="/site/auth?authclient=vkontakte" title="VKontakte"><span
                                            class="auth-icon vkontakte"></span></a></li>
                            <?php } ?>

                            <?php if (!in_array('facebook', $sources)) { ?>
                                <li><a class="facebook auth-link" href="/site/auth?authclient=facebook" title="Facebook"><span
                                            class="auth-icon facebook"></span></a></li>
                            <?php } ?>

                            <?php if (!in_array('google', $sources)) { ?>
                                <li><a class="google auth-link" href="/site/auth?authclient=google" title="Google"><span
                                            class="auth-icon google"></span></a></li>
                            <?php } ?>

                            <?php if (count($sources) > 0) { ?>
                                <div class="mb60"></div>
                            <?php } ?>

                        </ul>
                    </div>
                </div>

            <?php } ?>

            <h1 class="mb30">Изменить e-mail</h1>
            <h4><b>Текущий e-mail:</b> <?= \common\models\User::findOne(\Yii::$app->user->getId())->email ?></h4>
            <div class="row mb30">
                <div class="col-xs-6">
                    <? $form = \yii\widgets\ActiveForm::begin() ?>
                    <?= $form->field($change_email_form, 'email')->textInput([]) ?>
                    <?= \yii\helpers\Html::submitInput('Изменить', [
                        'value' => 'save-proile',
                        'class' => 'btn btn-success save-user-data pull-right '
                    ]) ?>
                    <? $form::end() ?>

                </div>
            </div>
            <h1 class="mb30">Изменить пароль</h1>
            <div class="row">
                <div class="col-xs-6">
                    <? $form = \yii\widgets\ActiveForm::begin() ?>
                    <?= $form->field($change_password_form, 'password')->passwordInput([]) ?>
                    <?= $form->field($change_password_form, 'new_password')->passwordInput([]) ?>
                    <?= $form->field($change_password_form, 'new_password_repeat')->passwordInput([]) ?>
                    <?= \yii\helpers\Html::submitInput('Изменить', [
                        'value' => 'save-proile',
                        'class' => 'btn btn-success save-user-data pull-right '
                    ]) ?>
                    <? $form::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>