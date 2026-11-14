<?php

use common\components\multilocal\Local;
use common\models\LocalCategory;
use common\models\User;
use frontend\modules\citydetector\CityDetector;
use frontend\widgets\CartCounter;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Html;
use yii\helpers\Url;

$userIsGuest = Yii::$app->user->isGuest;

if ($userIsGuest) {
    $userLabel = null;
} else {
    $user = User::findOne(\Yii::$app->user->getIdentity()->getId());
    $userLabel = $user->email;
}

?>
<header id="header">
    <?php if (\common\models\sale\March8::isEnabled()) { ?>
        <?php
        $session = Yii::$app->session;
        $key = 'accept_cookies_policy';
        $this->registerJs(<<<JS
    $('#cookies-alert .dismiss-alert').on('click', function() {
        $('#cookies-alert').hide();
        $.ajax({
            url: '/site/accept-cookies-policy',
            type: 'POST',
            data: {accept: true},
        });
    });
JS
        );
        if (!$session->has($key)) {
            $this->registerJsFile('/js/jquery.countdown.min.js', ['depends' => ['yii\web\JqueryAsset']]);
            $time = date('Y/m/d', mktime(0,0,0,3,9,2018));
            $this->registerJs(<<<JS
            $("#subheader-counter")
                .countdown("$time", function(event) {
                    $(this).text(
                        event.strftime('%D:%H:%M:%S')
                    );
                });
JS
            )
            ?>
            <section id="cookies-alert" style="background: #fd3a3a; color: #fff; padding: 15px;">
                <div class="container">
                    Весенние скидки до 50% на все товары - осталось <span id="subheader-counter"></span>
                    <a class="a" href="<?= \yii\helpers\Url::to(['/site/eight-march']) ?>" style="color: #fff;">
                        Подробнее
                    </a>
                    <div class="dismiss-alert pull-right pointer">×</div>
                </div>
            </section>
        <?php } ?>
    <?php } ?>
    <div class="container">

        <div id="go-up" class="goHome"><?= \Yii::t('app', 'Go up') ?></div>

        <a href="<?= \Yii::$app->homeUrl ?>" class="logo"></a>

        <nav id="nav">

            <div class="humburger">

                <span class="hidden-xs hidden-sm">
                    <?= \Yii::t('app', 'Catalog') ?><i class="fa fa-chevron-right"></i>
                </span>

                <i class="fa fa-bars"></i>

                <ul id="catalog">
                    <li class="closer">
                        <div class="close-catalog"></div>
                        <div class="close-nav"></div>
                    </li>
                    <?php LocalCategory::renderTree(); ?>
                </ul>

            </div>

            <ul id="navigation">

                <li class="closer">
                    <div class="close-nav"></div>
                </li>

                <?php if (Local::detectLocal() == Local::RU) { ?>
                    <li>
                    <span class="call-city-picker a hidden-md hidden-lg hideBorderTop">
                        <i class="fa fa-map"></i>
                        <?= \Yii::t('app', 'City') ?>:
                        <span class="location-city"><?= CityDetector::getCity() ?></span>
                    </span>
                    </li>
                <?php } ?>

                <li>
                    <span class="a catalog-opener hidden-md hidden-lg">
                        <i class="fa fa-star"></i> <?= \Yii::t('app', 'Catalog') ?> <span class="right-arrow"></span>
                    </span>
                </li>

                <li>
                    <?= Html::a('<i class="fa fa-tag"></i> '.\Yii::t('app', 'Special deals'),
                        ['/catalog/stock'],
                        ['class' => 'hidden-md hidden-lg']) ?>
                </li>

                <?php if (Local::detectLocal() == Local::RU) { ?>
                    <li class="open-childs--on-hover">
                    <span class="a open-childs block">
                        <i class="fa fa-globe hidden-lg hidden-md"></i>
                        <?= \Yii::t('app', 'Mad Wave World') ?>
                        <span class="right-arrow rotate visible-xs visible-sm"></span>
                    </span>
                        <ul class="slide-content">
                            <li><?= Html::a(\Yii::t('app', 'Blog'), ['/world/index']) ?></li>
                            <li><?= Html::a(\Yii::t('app', 'MadWave Challenge'), ['/challenge']) ?></li>
                            <li><?= Html::a(\Yii::t('app', 'About'), ['/site/about']) ?></li>
                            <li><?= Html::a(\Yii::t('app', 'Vladimir Morozov'), ['/site/vladimir-morozov']) ?></li>
                            <li><?= Html::a('Тренировочные сборы', 'http://camp.yoswim.ru', [
                                    'rel' => 'nofollow'
                                ]) ?></li>
                        </ul>
                    </li>
                <?php } else { ?>
                    <li class="small"><?= Html::a(\Yii::t('app', 'Russia'), 'https://www.madwave.ru') ?></li>
                    <li class="small"><?= Html::a(\Yii::t('app', 'About'), ['/site/about']) ?></li>
                <?php } ?>

                <li>
                    <?php if ($userIsGuest) { ?>
                        <span class="a hidden-md hidden-lg openSignupModal">
                            <i class="fa fa-user"></i>
                            <?= \Yii::t('app', 'Log in').'/'.\Yii::t('app', 'Sign up') ?>
                        </span>
                    <?php } else { ?>
                        <?= Html::a('<i class="fa fa-user"></i>'.$userLabel, ['/account/orders'], [
                            'class' => 'hidden-md hidden-lg'
                        ]); ?>
                    <?php } ?>
                </li>

                <li class="small"><?= Html::a(\Yii::t('app', 'Delivery'), ['/site/delivery']) ?></li>

                <?php if (Local::detectLocal() == Local::RU) { ?>
                    <li class="small"><?= Html::a(\Yii::t('app', 'Stores'), ['/site/stores']) ?></li>
                <?php } ?>

                <?php if ($userIsGuest) { ?>
                    <li class="small">
                        <span class="a openBonusModal">
                            <?= \Yii::t('app', 'Client card') ?> <i class="fa fa-credit-card"></i>
                        </span>
                    </li>
                <?php } ?>

                <?php if (Local::detectLocal() == Local::RU) { ?>
                    <li class="small visible-xs visible-sm"><?= Html::a(\Yii::t('app', 'Contact'), Url::to(['/site/contacts'])) ?></li>
                    <li class="small"><?= Html::a('B2B', ['/site/redirect', 'path' => 'http://b2b.madwave.ru/', true]) ?></li>
                    <li class="visible-xs visible-sm">
                        <br>
                        <br>
                        <span class="color-primary h3">8 800 100 57 19</span>
                        <br><span style="color: #fff">Звонок по россии бесплатный</span>
                    </li>
                <?php } ?>

            </ul>
        </nav>

        <div class="pull-right nav-right">

            <div class="hidden-xs hidden-sm">
                <?php if ($userIsGuest) { ?>
                    <span class="openSignupModal pointer"><i class="fa fa-user"></i></span>
                <?php } else {
                    echo ButtonDropdown::widget([
                        'label' => '<i class="fa fa-user"></i> <span style="margin-right: 10px">'.$userLabel.'</span> ',
                        'dropdown' => [
                            'items' => [
                                ['label' => \Yii::t('app', 'My Account'), 'url' => ['/account/orders']],
                                ['label' => \Yii::t('app', 'Log out'), 'url' => ['/site/logout'],
                                    'linkOptions' => [
                                        'data-method' => 'post',
                                    ]
                                ],
                            ],
                        ],
                        'encodeLabel' => false,
                        'options' => [
                            'class' => 'user pull-right'
                        ]
                    ]);
                } ?>
            </div>

            <span class="pipe">|</span>

            <?= Html::a(CartCounter::widget(), ['/checkout/cart/index'], ['class' => 'cart-container']) ?>

            <span class="pipe">|</span>

            <i class="search-submit fa fa-search"></i>

        </div>

    </div>
</header>