<?php

use common\components\multilocal\Local;
use common\components\screaming_crawl\models\CrawlDiffs;
use common\components\screaming_crawl\ScreamingCrawl;
use common\models\content\OverriddenMeta;
use common\models\content\TopBanner;
use common\models\Feature;
use frontend\widgets\easyfeatures\EasyFeatures;
use frontend\widgets\ViewedProducts;
use soovorow\product_features\widgets\Features;

/**
 * @var $this yii\web\View
 * @var $model \common\models\Product
 *
 * @var $category \common\models\LocalCategory
 *
 * @var $related_products string
 * @var $viewed_products string
 * @var $product_reviews string
 *
 */

$category_title = '';
if ($category = $model->getLocalCategory()) {
    if ($parent = $category->getParent()) {
        $this->params['breadcrumbs'][] = ['label' => $parent->getTitle(), 'url' => $parent->getUrl()];
    }
    $this->params['breadcrumbs'][] = ['label' => $category->getTitle(), 'url' => $category->getUrl()];
    $category_title = ' | ' . $category->getTitle();
}
$this->params['breadcrumbs'][] = $model->name;

$title = $model->name . $category_title . ' | ' . 'Mad Wave';

if ($diff = CrawlDiffs::find()->where(['CanonicalLinkElement1' => ScreamingCrawl::cutUrl($model->getUrl())])->one()) {
    $title = $diff->Title1;
};

if ($model->overriddenMeta && Local::detectLocal() == Local::RU) {
    $this->title = $model->overriddenMeta->meta_title_ru;
    $this->registerMetaTag(['name' => 'description', 'content'  => $model->overriddenMeta->meta_description_ru]);
} else {
    $this->title = $title;
    $this->registerMetaTag(['name' => 'description', 'content'  => $model->getMetaDescription()]);
}


$viewUrl = in_array($model->id, [876, 501, 1497, 1498, 1493, 1272, 1385, 1384, 1383]) ? 'load-new-view' : 'load-view';

$this->registerJs(<<<JS
    $('body').on('click', '[data-loadview]', function () {
        var container =  $('#variant');
        $.ajax({
            'type': 'post',
            'url': '/ajax/$viewUrl',
            'data': {'sku': $(this).data('loadview')},
            'beforeSend': function () {container.addClass('loading')},
            'complete': function () {container.removeClass('loading')},
            'success': function (response) {
                var r = JSON.parse(response);
                container.html($(r.view));
                window.history.pushState(null,'hi','?sku='+r.sku);
                zoom();
                ga('send', 'pageview', window.location.href );
            }
        })
    })

JS
);

?>

<?= \common\widgets\product\page\TopBanner::widget(['model' => $model->topBanner]) ?>

<div class="container">
    <div class="product-view row mb60">
        <div id="variant" class="ajax-loading">
            <?php if (in_array($model->id, [876, 501, 1497, 1498, 1493, 1272, 1385, 1384, 1383, 876])) {
                echo $this->render('_view_new', ['model' => $model, 'v' => $v]);
            } else {
                echo $this->render('_view', ['model' => $model, 'v' => $v]);
            } ?>
        </div>
    </div>
</div>

    <?php

    $cases = [1127, 1126, 1125];
    if (in_array($model->id, $cases)) { ?>

        <style>
            #product_video {
                cursor: pointer;
                padding: 75px 10% 75px;
                position: relative;
            }

            @media (min-width: 768px) {
                #product_video {
                    padding: 75px 10% 75px 50%;
                }
            }

            #product_video .swimmer {
                position: absolute;
                left: 3%;
                bottom: 0;
                height: 425px;
                width: 569px;
                background: url("/images/extra-banners/video-man.png") center center / contain no-repeat;
            }

            #product_video h3 {
                position: relative;
                text-transform: uppercase;
                font-weight: 300;
                color: #fff;
                font-size: 22px;
                margin-bottom: 30px;
            }

            #product_video p {
                color: #8ec8db;
                position: relative;
                margin-bottom: 20px;
            }

            #product_video > span {
                position: relative;
                display: block;
                color: #fff;
                text-transform: uppercase;
            }
            #product_video .fa {
                font-size: 66px;
                color: #efefef;
            }
        </style>
        <?php
        $this->registerJs('
                jQuery(\'#product_video\').lightbox();
            ')
        ?>
        <div style="background: url('/images/extra-banners/video-bg.jpg') center center / cover no-repeat;
            margin-top: 60px;">
            <div class="container">
                <section id="product_video"
                         class="clearfix"
                         href="https://www.youtube.com/embed/u_Zfxj5xxfo"
                         data-width="640"
                >
                    <div class="swimmer"></div>
                    <h3>Усовершенствованная гидродинамика <br> и превосходный фронтальный обзор</h3>
                    <p>
                        Очки Record Breaker представляют собой совершенное соотношение
                        скорости и надежности на стартах. Благодаря инновационному
                        ультракомфортабельному обтюратору и гидродинамической форме вы не
                        потеряете на стартах ни единой доли секунды.
                    </p>
                    <p>
                        Специальная конструкция линз, разработанная специалистами компании
                        Mad Wave позволит сохранить идеальное положение головы и тела в воде
                    </p>
                    <span><span class="fa fa-play-circle-o"></span><span style="position: relative;top: -19px;margin-left: 20px;"><?= Yii::t('app', 'WATCH THE VIDEO') ?></span></span>
                </section>
            </div>
        </div>

    <?php } ?>

<?php if ($model->id == 1272) { ?>
    <section class="feature-new" style="background: url('/images/new_features/FINAL.jpg') center center / cover no-repeat">
        <div class="container text-center">
            <h2><?= Yii::t('app', 'MANAGE YOUR MOVEMENT') ?></h2>
            <p class="col-md-6 col-md-push-3"><?= Yii::t('app', 'Manage resistance, load, and movement in the water without the slightest risk of injuring yourself, even with sudden
starts.') ?></p>
        </div>
    </section>
<?php } ?>

<?php if (in_array($model->id, [1385, 1384, 1383])): ?>
    <section class="feature-new" style="background: url('/images/new_features/1385-benefit.jpg') center center / cover no-repeat">
        <div class="container text-center">
            <h2><?= Yii::t('app', 'DESIGN AND COMFORT') ?></h2>
            <p class="col-md-6 col-md-push-3">
                <?= Yii::t('app', 'With a leading, aggressive design and impeccable, ergonomic fit, Mad Wave&#39;s ALIEN goggles allow you to get the
most out of daily swim practices.') ?>
            </p>
        </div>
    </section>
<?php endif ?>

<?php if (in_array($model->id, [1277])): ?>
    <style>
        @media (min-width:992px){
            .lane-one {
                background: url('/images/new_features/lane-one.jpg') center center / cover no-repeat !important;
            }
            .lane-two {
                background: url('/images/new_features/lane-two.jpg') center center / cover no-repeat !important;
            }
            .lane-three {
                background: url('/images/new_features/lane-three.jpg') center center / cover no-repeat !important;
            }
        }
    </style>
    <section class="product-feature text-top lane-one text-center-xs text-center-sm" style="background: url('/images/new_features/lane-one-sm.jpg') center center / cover no-repeat">
        <div class="container">
            <div class="col-md-6">
                <h2><?= Yii::t('app', 'SPACIOUS BACK POCKET') ?></h2>
                <p>
                    <?= Yii::t('app', 'Spacious, ventilated back pocket with a special compartment for notebooks (up to 13&quot;) allows you to bring even large equipment with you') ?>
                </p>
                <p>
            </div>
        </div>
    </section>
    <section class="product-feature text-top  lane-two inverse  text-center-xs text-center-sm" style="background: url('/images/new_features/lane-two-sm.jpg') center center / cover no-repeat">
        <div class="container">
            <div class="col-md-6  col-md-push-6">
                <h2><?= Yii::t('app', 'MULTIFUNCTIONAL FRONT POCKET') ?></h2>
                <p>
                    <?= Yii::t('app', 'Allows you to pack things with maximum convenience, contains 4 separate inner compartments for goggles, textiles, and even small items such as paddles') ?>
                </p>
            </div>
        </div>
    </section>
    <section class="product-feature text-top  lane-three text-center-xs text-center-sm" style="background: url('/images/new_features/lane-three-sm.jpg') center center / cover no-repeat">
        <div class="container">
            <div class="col-md-6">
                <h2><?= Yii::t('app', 'SHOE COMPARTMENT') ?></h2>
                <p>
                    <?= Yii::t('app', 'In the lower ventilated compartment of the Lane backpack, you can fit up to 2 pairs of shoes or even a pair of short training fins') ?>
                </p>
            </div>
        </div>
    </section>
<?php endif ?>

<?php if (in_array($model->id, [501, 1493])): ?>
    <style>
        .product-feature.design-airforce {
            background: url('/images/forceshall_banner.jpg') center center / cover no-repeat !important;
            padding: 240px 0 !important;
        }
        @media (max-width: 798px) {

            .product-feature.design-airforce {
                padding: 60px 15px !important;
                background: url('/images/forceshall_banner.jpg') 70% center / cover no-repeat !important;
            }
        }

    </style>

    <section class="product-feature design-airforce inverse">
        <div class="container">
            <div class="col-md-6">
                 <h2><?= Yii::t('app', 'DESIGN AIR FORCE') ?></h2>
                 <p>
                    <?= Yii::t('app', 'Daring, aggressive design, inspired by one of the fastest fighters in the world, allows you to feel maximum confidence at starts and be full of energy for new victories and achievements.') ?>
                 </p>
            </div>
        </div>
    </section>

    <div class="features" style="background: #fff; padding: 0">
        <div class="container">
            <div class="col-sm-6 col-sm-push-6" style="padding-top: 90px; padding-bottom: 60px">
                <h2>
                    <?= Yii::t('app', 'NOW 2X STRONGER') ?>
                </h2>
                <div class="product-desc">
                    <?= Yii::t('app', 'The fabric\'s mesh structure using reinforced polyester fibers provides uniform compression without weighing down the fabric while double-sided gluing technology on the seams increases the durability of the suit by more than 2x.') ?>
                    <br>
                    <br>
                    <img src="/images/fina.png" style="max-width: 100%" alt="fina approved">
                </div>
            </div>
            <div class="col-sm-6 col-sm-pull-6">
                <?php if ($model->id == 1493) { ?>
                <?= EasyFeatures::widget([
                    'image' => Yii::getAlias('@web/images/forceshall_banner_women.jpg'),
                    'items' => [
                        [
                            'title' => Yii::t('app', 'Ultra-strong glued seams'),
                            'description' => Yii::t('app', 'double-sided gluing technology on the seams increases the durability of the suit by more
than 2x'),
                            'x' => 38,
                            'y' => 26
                        ],
                        [
                            'title' => Yii::t('app', 'High compression level'),
                            'description' => Yii::t('app', 'ideal for overcoming sprinter distances'),
                            'x' => 61,
                            'y' => 72
                        ],
                        [
                            'title' => Yii::t('app', 'MW Lotus Fabric Technology'),
                            'description' => Yii::t('app', 'the fabric&#39;s surface doesn&#39;t absorb almost any moisture'),
                            'x' => 47,
                            'y' => 54
                        ],
                        [
                            'title' => Yii::t('app', 'The fabric&#39;s mesh structure using reinforced polyester fibers'),
                            'description' => Yii::t('app', 'provides uniform compression without weighing down the fabric'),
                            'x' => 51,
                            'y' => 39
                        ],
                        [
                            'title' => Yii::t('app', 'Silicone seals above the knee'),
                            'description' => Yii::t('app', 'provide ideal fit for the suit'),
                            'x' => 44,
                            'y' => 84
                        ],
                        [
                            'title' => Yii::t('app', 'Open back'),
                            'description' => Yii::t('app', 'provides maximum freedom of movement'),
                            'x' => 33,
                            'y' => 36
                        ],
                    ],
                ]); ?>
                <?php } ?>


                <?php if ($model->id == 501) { ?>
                    <?= EasyFeatures::widget([
                        'image' => Yii::getAlias('@web/images/forceshall_banner_men.jpg'),
                        'items' => [
                            [
                                'title' => Yii::t('app', 'Ultra-strong glued seams'),
                                'description' => Yii::t('app', 'double-sided gluing technology on the seams increases the durability of the suit by more
than 2x'),
                                'x' => 58,
                                'y' => 35
                            ],
                            [
                                'title' => Yii::t('app', 'High compression level'),
                                'description' => Yii::t('app', 'ideal for overcoming sprinter distances'),
                                'x' => 85,
                                'y' => 56
                            ],
                            [
                                'title' => Yii::t('app', 'MW Lotus Fabric Technology'),
                                'description' => Yii::t('app', 'the fabric&#39;s surface doesn&#39;t absorb almost any moisture'),
                                'x' => 62,
                                'y' => 62
                            ],
                            [
                                'title' => Yii::t('app', 'The fabric&#39;s mesh structure using reinforced polyester fibers'),
                                'description' => Yii::t('app', 'provides uniform compression without weighing down the fabric'),
                                'x' => 51,
                                'y' => 50
                            ],
                            [
                                'title' => Yii::t('app', 'Silicone seals above the knee'),
                                'description' => Yii::t('app', 'provide ideal fit for the suit'),
                                'x' => 55,
                                'y' => 81
                            ],
                        ],
                    ]); ?>
                <?php } ?>
            </div>
        </div>
    </div>

<?php endif ?>

<?php if (in_array($model->id, [1497, 1498])): ?>

<div class="feature-morozov feature-morozov1">

    <div class="container">
        <div class="col-md-6">
             <h2>
                 <?= Yii::t('app', 'NOW 2X STRONGER') ?>
             </h2>
             <p>
                 <?= Yii::t('app', 'The technology of double-sided gluing used on the seams with ultrasound application increases the strength of the racing suit by more than 2x, providing you with the highest reliability at the most crucial moments.') ?>
             </p>
        </div>
    </div>
</div>
<div class="feature-morozov feature-morozov2" style="color: #fff">

    <div class="container">
        <div class="col-md-6"></div>
        <div class="col-md-6">
            <h2 class="text-right"><?= Yii::t('app', 'MW LOTUS FABRIC TECHNOLOGY') ?></h2>
            <p class="text-right"><?= Yii::t('app', 'Thanks to MW LOTUS technology, the contact angle of the water with the material is increased from 40 degrees - which is typical of normal fabric- to 133, which makes the outer layer of the racing suit practically waterproof, without moisture absorption.') ?></p>
            <div class="col-sm-6 col-sm-push-6">
            </div>
        </div>
    </div>
</div>
<div class="feature-morozov feature-morozov3">
    <div class="container">
            <div class="col-md-6">
                    <h2><?= Yii::t('app', 'NETTED STRUCTURE') ?></h2>
                    <p><?= Yii::t('app', 'The netted structure of the material with reinforced polyester fibers allows even compression without weighing down the fabric, providing you with maximum comfort and a reliable fit.') ?></p>
            </div>
    </div>
</div>
<?php endif; ?>

<?php if (in_array($model->id, [992])): ?>
    <style>
        @media (min-width:992px){
            .mad-team-one {
                background: url('/images/new_features/mad-team-one.jpg') center center / cover no-repeat !important;
            }
            .mad-team-two {
                background: url('/images/new_features/mad-team-two.jpg') center center / cover no-repeat !important;
            }
            .mad-team-three {
                background: url('/images/new_features/mad-team-three.jpg') center center / cover no-repeat !important;
            }
        }
    </style>
    <section class="product-feature mad-team-one text-top text-center-xs text-center-sm" style="background: url('/images/new_features/mad-team-one-sm.jpg') center center / cover no-repeat">
        <div class="container">
            <div class="col-md-6">
                <h2><?= Yii::t('app', 'VERY SPACIOUS') ?></h2>
                <p>
                    <?= Yii::t('app', 'You can easily fit large items in the backpack, such as swimming boards, pull buoys, or fins') ?>
                </p>
                <p>
            </div>
        </div>
    </section>
    <section class="product-feature inverse mad-team-two text-top text-center-xs text-center-sm" style="background: url('/images/new_features/mad-team-two-sm.jpg') center center / cover no-repeat">
        <div class="container">
            <div class="col-md-6  col-md-push-6">
                <h2><?= Yii::t('app', 'LARGE SIDE POCKETS WITH VENTILATION') ?></h2>
                <p>
                    <?= Yii::t('app', 'These side pockets allow you to easily separate wet and dry items') ?>
                </p>
            </div>
        </div>
    </section>
    <section class="product-feature mad-team-three text-center-xs text-center-sm" style="background: url('/images/new_features/mad-team-three-sm.jpg') center center / cover no-repeat">
        <div class="container">
            <div class="col-md-6">
                <h2><?= Yii::t('app', 'SHOE COMPARTMENT') ?></h2>
                <p>
                    <?= Yii::t('app', 'In the lower ventilated compartment of the Mad Team backpack, you can fit up to 2 pairs of shoes or even a pair of short training fins') ?>
                </p>
            </div>
        </div>
    </section>

<?php endif ?>

<?php if (in_array($model->id, [1272, 1385, 1384, 1383])): ?>
    <div class="features">
        <div class="container">
            <div class="col-sm-6 col-center">
                <?php if ($model->id == 1272): ?>
                    <h2><?= Yii::t('app', 'MADE FOR PROFESSIONALS') ?></h2>
                    <div class="product-desc">
                        <?= Yii::t('app', 'The DRAG CHUTE trainer is now equipped with an automatic-open function, as well as a regulating outlet valve for
changing the resistance load. A special cushioned cable provides a minimum load on the spine during sharp starts, and a
convenient float allows you to regulate the level of the parachute load while avoiding interference with your legs. In order
to use, you must have a removable swimming belt.') ?>
                    </div>
                <?php endif ?>
                <?php if (in_array($model->id, [1385, 1384, 1383])): ?>
                    <h2><?= Yii::t('app', 'EVERYTHING YOU NEED') ?></h2>
                    <div class="product-desc">
                        <?= Yii::t('app', 'Wide and soft seals minimize strain on the eyes, and the innovative construction of ALIEN goggles provides excellent
fit and convenience in use. Lenses with enhanced side view, as well as protection from Ultraviolet rays and Anti-fog
coating. Each set includes 4 interchangeable nasal liners for perfect fit.') ?>
                    </div>
                <?php endif ?>
            </div>
            <div class="col-sm-6 col-center">
                <?php $image = '';
                if ($model->id == 1272)
                    $image = 'drag-chute';
                elseif (in_array($model->id, [1385, 1384, 1383]))
                    $image = 'alien-rainbow'
                ?>
                <?php $modelId = in_array($model->id, [1385, 1384, 1383]) ? 1385 : $model->id; ?>
                <?php if (Feature::getByProductId($modelId)): ?>
                    <?= EasyFeatures::widget([
                        'image' => Yii::getAlias('@web/images/featwidget/'.$image.'.png'),
                        'items' => Feature::generateWidgetItems($modelId),
                    ]); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!in_array($model->id, [501, 1497, 1498, 1493, 1272, 1385, 1384, 1383, 1277, 992]) && Local::detectLocal() == Local::RU): ?>
    <div class="container" >
        <div class="product-view row mb60" style="padding-top: 0">
            <?= Features::widget(['product_id' => $model->id]) ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($model->id === 876) { ?>
    <style>
        .product-feature.fins-training {
            background: url('/images/fins_traning_banner.gif') center center / cover no-repeat !important;
            padding: 240px 0 !important;
        }
        @media (max-width: 798px) {

            .product-feature.fins-training {
                padding: 60px 15px !important;
                background: url('/images/fins_traning_banner.gif') center center / cover no-repeat !important;
            }
        }
    </style>
    <section class="product-feature inverse fins-training">
        <div class="container">
            <div class="col-md-6">
                <h2 class="design-airforce">
                    <?= Yii::t('app', 'MADE TO IMPRESS') ?>
                </h2>
                <p>
                    <?= Yii::t('app', 'FINS TRAINING professional short training fins will not only help perfect your technique, they will also emphasize your unique and unmatchable style') ?>
                </p>
            </div>
        </div>
    </section>

<?php } ?>

<div class="shadow">
    <div class="container">
        <div class="row mb60">

            <div class="col-xs-12">
                <?= \common\widgets\product\page\RelatedProducts::widget(['product' => $model]) ?>
            </div>

            <div class="col-xs-12">
                <?= \common\widgets\product\page\Reviews::widget(['product' => $model, 'v' => $v]) ?>
            </div>

            <div class="col-xs-12">
                <?= \common\widgets\product\page\ViewedProducts::widget() ?>
            </div>

        </div>
    </div>
</div>