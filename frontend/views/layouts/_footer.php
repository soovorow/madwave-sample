<?php

use common\components\multilocal\Local;
use common\models\AppString;
use frontend\models\Subscribe;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

if (Local::detectLocal() == 'ru-RU') {
    Subscribe::widget();
}

$ruFooterLinks = [
    Html::a(Yii::t('app', 'Публичная оферта'), ['/site/terms']),
    Html::a(Yii::t('app', 'Franchising'), ['/site/franchise']),
    Html::a(Yii::t('app', 'Contact'), ['/site/contacts']),
    Html::a(Yii::t('app', 'Up to 40% Clearance'), ['/site/discount-offers']),
    Html::a(Yii::t('app', 'Returns and exchange'), ['/site/returns-exchange']),
    Html::a(Yii::t('app', 'Size-guide'), ['/site/size-guide']),
];

$euFooterLinks = [
    Html::a(Yii::t('app', 'Contact'), ['/site/contacts']),
    Html::a(Yii::t('app', 'Distribution'), ['/site/distribution']),
    Html::a(Yii::t('app', 'Russia'), 'https://www.madwave.ru'),
    Html::a(Yii::t('app', 'B2B'), 'http://b2b.madwave.eu'),
    Html::a(Yii::t('app', 'Size-guide'), ['/site/size-guide']),
];

?>

<section id="footer">
    <div class="container p0">
        <div class="col-md-3 hidden-xs hidden-sm">
            <div class="paysystems">
                <i class="maestro"></i>
                <i class="paypal"></i>
                <i class="mastercard"></i>
                <i class="visa"></i>
            </div>
        </div>
        <div class="col-md-4 contact p0 text-center-xs">
            <?= AppString::getString('footer_contact_info') ?>
        </div>
        <div class="col-md-5 p0 footer-links text-center-xs">
            <?= implode(Local::choose([
                Local::RU => $ruFooterLinks,
                Local::EU => $euFooterLinks
            ]))?>
        </div>
    </div>
</section>

<section id="sub-footer">
    <div class="sub">
        <div class="container p0">
            <div class="col-md-3">
                <div class="logo"></div>
            </div>
            <div class="col-md-9 text-center-xs p0">
                <span class="a"><?= Yii::t('app', 'All rights reserved') ?></span>
                <br class="visible-xs">
                <span class="hidden-xs"><?= Yii::t('app', 'Office\'s address') ?>:</span> <?= AppString::getString('footer_address') ?>
                <div class="goHome pull-right"><?= Yii::t('app', 'Go up') ?></div>
            </div>
        </div>
    </div>
</section>

<section id="search-modal">
    <div class="container">
        <h2 class="inline-block"><?= Yii::t('app', 'Search') ?></h2>
        <span class="search-modal__close">×</span>
        <input id="search-input" type="text" class="form-control rad0" placeholder="<?= Yii::t('app', 'Search') ?>...">
        <div id="search__result" class="ajax-loading">
            <?= $this->render('../layouts/search-empty-state') ?>
        </div>
    </div>
</section>



<div class="modal fade" id="bonusModal" tabindex="-1" role="dialog">
    <?php
    $s = \Yii::$app->session;
    $k = 'openPopUp123412341234';
    $v = $s->get($k);

    if (\Yii::$app->user->isGuest && $v == 2 && Local::detectLocal() == Local::RU) {
        $this->registerJs("$('#bonusModal').modal();", \yii\web\View::POS_END);
    } else {
        $s->set($k, 0);
    }

    $s->set($k, $v+1);
    ?>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button class="close" type="button" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                <div class="title">
                    <span class="accent">
                        <?= Yii::t('app', 'Up to') ?> 60%
                    </span>
                    <span class="sub-accent">
                        <?= Yii::t('app', 'For registration') ?>
                    </span>
                    <span class="btn btn-default btn-lg openSignupModal">
                        <?= Yii::t('app', 'Get discount') ?>
                    </span>
                    <span class="btn btn-danger btn-lg" type="button" data-dismiss="modal">
                        <?= Yii::t('app', 'Not now') ?>
                    </span>
                </div>
                <small>* <?= AppString::getString('popup_asterisk') ?></small>
                <span class="products">

                    <img src="/images/homepage_pins/pin02.png" alt="1" style="width: 200px;left: -60px;top: -65px;position: absolute;">

                    <img src="/images/homepage_pins/pin04.png" alt="1" style="position: absolute;right: -84px;width: 235px;top: -30px;">

                    <img src="/images/homepage_pins/pin2.png" alt="1" style="position: absolute;width: 268px;top: 114px;left: -66px;">

                    <span class="disc-cycle" style="left: 10px;top: 10px;">-50%</span>
                    <span class="disc-cycle" style="top: 75px;">-50%</span>
                    <span class="disc-cycle" style="top: 180px;left: 110px;">-50%</span>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="signupModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content clearfix">
            <div class="modal-body">
                <div>

                    <button class="close" type="button" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home">
                            <?php
                            $model = new \frontend\models\user\LoginForm();
                            $form = ActiveForm::begin([
                                'action' => '/login'
                            ]); ?>

                            <div class="h4"><?= Yii::t('app', 'Login to your account') ?></div>

                            <?= $form->field($model, 'email') ?>
                            <?= $form->field($model, 'password')->passwordInput() ?>

                            <div class="clearfix">
                                <?= Html::a(Yii::t('app', 'Forget your password?'), ['/site/request-password-reset'], [
                                    'style' => 'display: inline-block;padding: 11px 0;',
                                    'class' => 'a'
                                ]) ?>
                                <?= Html::submitButton(Yii::t('app', 'Log in'), [
                                    'class' => 'btn btn-primary pull-right',
                                    'name' => 'login-button'
                                ]) ?>
                            </div>

                            <div class="horizont"><span><?= Yii::t('app', 'or') ?></span></div>

                            <div class="social">
                                <span><?= Yii::t('app', 'Sign in through') ?>:</span>
                                <?= yii\authclient\widgets\AuthChoice::widget([
                                    'baseAuthUrl' => ['/site/auth'],
                                    'popupMode' => true,
                                ]) ?>
                            </div>

                            <a href="#profile" class="a" aria-controls="profile" role="tab" data-toggle="tab">
                                <?= Yii::t('app', 'Create a new account') ?>
                            </a>
                            <?php ActiveForm::end(); ?>

                        </div>
                        <div role="tabpanel" class="tab-pane" id="profile">
                            <?php
                            $model = new \frontend\models\user\SignupForm();
                            $form = ActiveForm::begin(['action' => '/signup']); ?>

                            <div class="h4"><?= Yii::t('app', 'Create a new account') ?></div>

                            <?= $form->field($model, 'email') ?>
                            <?= $form->field($model, 'password')->passwordInput() ?>

                            <div class="clearfix">
                                <a href="#home" aria-controls="home" role="tab" data-toggle="tab" style="display: inline-block;padding: 11px 0;"><?=  Yii::t('app', 'Go back') ?></a>
                                <?= Html::submitButton(Yii::t('app', 'Sign up now'), [
                                    'class' => 'btn btn-primary pull-right',
                                    'name' => 'signup-button'
                                ]) ?>
                            </div>

                            <div class="horizont"><span><?= Yii::t('app', 'Pay attention') ?></span></div>

                            <div class="social">
                                <?= AppString::getString('registration_opt_in') ?>
                            </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>

                </div>



            </div>

            <div class="background"></div>
        </div>
    </div>
</div>