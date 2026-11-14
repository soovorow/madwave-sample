<?php

/* @var $hitProductsPreview string */
/* @var $seasonSaleProduct ProductVariant */
/* @var $popularArticles BlogPost[] */

use common\components\multilocal\Local;
use common\models\ProductVariant;
use common\models\TheLook;
use frontend\widgets\ShopTheLookSlider;
use yii\helpers\Html;

/**
 * @var $homepageBanner string
 * @var $pinnedItems string
 * @var $aside_cc_desc string
 * @var $shotr_about string
 * @var $asideProduct string
 */
?>

<?php if (\common\models\sale\March8::isEnabled() && Local::detectLocal() == Local::RU) {
    $this->registerJsFile('/js/jquery.countdown.min.js', ['depends' => ['yii\web\JqueryAsset']]);
    $time = date('Y/m/d', mktime(0,0,0,3,9,2018));
    $this->registerJs(<<<JS
    $("#counter")
        .countdown("$time", function(event) {
            $(this).text(
                event.strftime('%D:%H:%M:%S')
            );
        });
JS
    )

    ?>

    <style>
        .banner.special {
            height: 400px;
            /*padding-top: 300px;*/
            position: relative;
            background: url(/special/8-march/banner_mobile.gif) center center / cover no-repeat
        }

        @media (min-width: 769px) {
            .banner.special {
                height: 550px;
                padding-top: 400px;
                background: url(/special/8-march/banner.gif) center center / cover no-repeat
            }
        }
    </style>
    <section style="background: #000; margin-bottom: 60px; position: relative">
<!--        <div class="container" style="position: relative">-->
            <div id="counter" class="text-center" style="
                    position: absolute;
                    width: 100%;
                    left: 0;
                    text-align: center;
                    font-size: 30px;
                    margin-top: 50px;
                    color: #fff;
                    z-index: 1;
                "></div>

            <div class="banner special text-center">
                <div style="position: absolute; bottom: 40px; text-align: center; width: 100%">
                    <a href="<?= \yii\helpers\Url::to(['/site/eight-march']) ?>"
                       class="btn btn-default"
                       style="
                       /*background: #55c1d0 !important;*/
                       /*border-color: #55c1d0 !important;*/
                       background: transparent;
                       border-color: #fff;
                       color: #fff !important;
                       bottom: 30px;
                       padding: 15px 30px;
                       font-weight: bold;"
                    >К ПОКУПКАМ</a>
                </div>
            </div>
<!--        </div>-->
    </section>

<?php } else { ?>
    <?= $homepageBanner ?>
<?php } ?>

<?= $pinnedItems ?>

<section id="shop-the-look" style="padding-top: 30px; margin-bottom: 30px">

    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <h2><b>SHOP <br> THE LOOK</b></h2>
                <p>
                    <?= Yii::t('app', 'Spectacular men&#39;s black training LOOK or light women&#39;s LOOK in pink tones - Mad Wave specialists will help you feel confident during trainings and holidays at the pool!') ?>

                </p>
                <hr style="width: 50px; margin-left: 0; border: 2px solid #333">
                <a class="btn btn-default" style="margin-bottom: 50px" href="<?= \yii\helpers\Url::to(['/look/index']) ?>">
                    <?= Yii::t('app', 'ALL LOOKS') ?>
                </a>
            </div>

            <div class="col-xs-12 col-md-9">
                <?= ShopTheLookSlider::widget([
                    'looks' => $looks = TheLook::find()->where(['status' => 1])->limit(3)->orderBy('weight DESC')->all(),
                    'model' => $looks[0]
                ]) ?>
            </div>

        </div>
    </div>
</section>

<?php if (Local::detectLocal() == Local::RU) { ?>
    <section id="home-2of3" style="margin-top: 60px">
        <div class="content">
            <a href="<?= \yii\helpers\Url::to(['site/morozov-prigoda']) ?>">Подробнее</a>
        </div>
    </section>
<?php } ?>

<section id="home-heats" class="container p0">
    <h2 class="col-xs-12">
        <?= Yii::t('app', 'Best sellers') ?>
    </h2>
    <div class="col-md-9 col-xs-12">
        <div class="home-products">
            <?= $hitProductsPreview ?>
        </div>
    </div>
    <div class="col-md-3 col-xs-12">

        <?= $asideProduct ?>

        <?php if (Local::detectLocal() == Local::RU): ?>

            <div class="aside season-sale" style="background: url(<?= Yii::getAlias('@web/images/layout/card-more.png') ?>) bottom right no-repeat">
                <p class="title zig-left"><?= Yii::t('app', 'Bonus card') ?></p>
                <h4>
                   <?= $aside_cc_desc ?>
                </h4>
                <span class="openBonusModal a">
                    <?= Yii::t('app', 'More') ?>
                </span>
            </div>

            <div class="pop-posts">
                <h3>Популярные статьи</h3>
                <ul class="media-list">

                    <?php foreach ($popularArticles as $article): ?>
                        <a href="<?= $article->getUrl() ?>" class="media">
                        <span class="media-left">
                            <span class="media-object"
                                  style="
                                          width: 64px;
                                          height: 64px;
                                          background: url('<?= $article->getCoverUrl(true) ?>') center center / cover no-repeat">
                            </span>
                        </span>
                            <span class="media-body">
                            <span class="media-heading">
                                <?= $article->title ?>
                            </span>
                                <?= date('d/m/y', $article->created_at) ?>
                        </span>
                        </a>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

    </div>
</section>

<?= $this->render('_pinned-categories') ?>

<section class="about container">
    <div class="col-md-8 col-md-push-2">
        <h3>Mad Wave - <?= Yii::t('app', 'is') ?></h3>
        <p class="text-center">
            <?= $shotr_about ?>
        </p>
        <?= Html::a(Yii::t('app', 'More'), ['site/about']) ?>
    </div>
</section>