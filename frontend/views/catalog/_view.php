<?php

use common\components\multilocal\Local;
use common\components\AdmitAd;
use common\components\Criteo;
use common\models\Testimonials;
use frontend\widgets\Rating;
use yii\helpers\Html;

/* @var $v common\models\ProductVariant */
/* @var $model \common\models\Product */

$v->measureDetailView();

if (Local::detectLocal() == Local::RU) {
    AdmitAd::setProductPage($v->id, $model->name, $model->category_id);
    Criteo::setProductPage($v->id);
}

$data = $model->getColorSizePickerData();
$colors = $data['colors'];
$isOneColorOneSize = $data['isOneColorOneSize'];

$currency = \Yii::$app->params['currency']

?>
<div class="clearfix">

    <div class="col-xs-12">
        <div class="top-caption">
            <h1 itemprop="name"><?= $model->getH1() ?></h1>
            <div class="pull-right">
                <?php echo Rating::widget([
                    'rating' => $model->getRating(),
                    'votesCount' => $model->getRatingVotes(),
                    'enableDescription' => true
                ]) ?>
            </div>
        </div>
        <?= \common\widgets\product\page\MarketingLabels::widget([
            'v' => $v,
            'model' => $model
        ]) ?>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12 p0">

        <div class="col-xs-12">
            <div class="view_img">
                <img id="zoom"
                     itemprop="image"
                     width="100%"
                     alt="<?= $v->product->globalCategory->getName(). ' ' . $v->name  ?>"
                     src="<?= $v->getImage(1, $v::IMG_LARGE, true, true) ?>"
                     data-zoom-image="<?= $v->getImage(1, $v::IMG_ORIGINAL, true, true) ?>" />

            </div>

            <div class="all_imgs clearfix">
                <?php for ($i = 1; $i <= 4; $i++) {
                    echo $v->getImage($i, $v::IMG_SMALL);
                } ?>
            </div>
        </div>

    </div>

    <div id="product-description-width" class="col-sm-6 col-md-8 col-xs-12"></div>

    <div class="col-md-8 col-xs-12">
        <div class="row">
            <div class="col-md-5 col-md-push-7 col-sm-6 col-xs-12  ">

                <div class="col-xs-12 cleaerfix add-to-cart-container">
                    <div class="col-xs-12">
                        <div class="row" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                            <meta itemprop="priceCurrency" content="RUB" />
                            <b class="titled cost"><?= \Yii::t('app', 'Price') ?></b>

                            <?php if (Local::detectLocal() == Local::RU) { ?>

                                <span class="price">
                                    <?php if ($v->salePrice) {?>
                                        <?php if (\Yii::$app->language == 'ru-RU') { ?>
                                            <span style="text-decoration: line-through; color: #999">
                                                <?= number_format($v->oldPrice, 0, '.', ' ') . $currency ?>
                                            </span>
                                        <?php } ?>
                                    <?php } elseif ($v->product->discount_card != 1) { ?>
                                        <?= number_format($v->price, 0, '.', ' ') . $currency ?>
                                    <?php } else { ?> &nbsp; <?php } ?>
                                </span>
                                <span class="target-price">
                                    <?php if ($v->salePrice || $v->product->discount_card == 1) {?>
                                        <span itemprop="price"><?= number_format($v->price, 0, '.', ' ') . $currency ?></span>
                                    <?php } elseif ($v->product->discount_card != 1) { ?>
                                        <span class="color-primary">
                                            <i class="fa fa-credit-card"></i>
                                            <span itemprop="price"><?= number_format($v->cardPrice, 0, '.', ' ') . $currency ?></span>
                                        </span>
                                    <?php } ?>
                                </span>

                            <?php } else { ?>

                                <span class="target-price">
                                        <span itemprop="price"><?= number_format($v->price, Local::choose([
                                                Local::RU => 0,
                                                Local::EU => 2
                                            ]), '.', ' ') . $currency ?></span>
                                </span>

                            <?php } ?>


                        </div>

                        <?php if (Local::detectLocal() == Local::RU && (!$v->salePrice && $v->product->discount_card != 1)) { ?>
                            <div class="row">
                                <b class="titled cost"><?= \Yii::t('app', 'Discount with card') ?></b>
                                <span class="card-discount">
                                <?= number_format(($v->price - $v->cardPrice), 0, '.', ' ') . $currency ?>
                            </span>
                            </div>
                        <?php } ?>

                        <?php if (count($colors) > 0) { ?>

                            <div class="row">
                                <b class="text-uppercase" style="font-size: 12px"><?= \Yii::t('app', 'Color') ?></b>
                                <br>
                                <br>
                                <div class="clearfix">
                                    <?php foreach ($colors as $c_id => $c) {
                                        $active = $v->color_id == $c_id;
                                        if ($c) {
                                        ?>
                                        <div class="col-xs-3 text-center colorpicker <?= $active ? 'active' : '' ?>"
                                             style="<?= $c['available'] ? '' : 'opacity: .3' ?>"
                                             data-loadview="<?= $c['sku'] ?>">
                                            <div style="background: url('<?= $c['image_url'] ?>') center center / contain no-repeat;"></div>
                                            <small><?= $c['label'] ?></small>
                                            <?php if ($isOneColorOneSize) {
                                                foreach ($c['sizes'] as $size) { ?>
                                                    <span style="white-space: nowrap;overflow: hidden;display: block;text-overflow: ellipsis;"><?= $size['label'] ?></span>
                                                <?php }
                                            } ?>
                                        </div>
                                    <?php }
                                    }?>
                                </div>
                            </div>

                        <?php } ?>

                        <?php if (Local::detectLocal() == Local::RU && in_array($v->product->id, [
                                1439,
                                1438,
                                1453,
                                1452
                            ])) { ?>
                        <div class="clearfix">
                            <div style="margin: 0 !important;" class="alert alert-warning">
                                <strong>
                                    Обратите внимание, что топ и плавки продаются отдельно.
                                </strong>
                            </div>
                            <br>
                        </div>
                        <?php } ?>

                        <?php if (Local::detectLocal() == Local::RU && in_array($v->product->id, [1266])) { ?>
                        <div class="clearfix">
                            <div style="margin: 0 !important;" class="alert alert-warning">
                                <strong>
                                    Обратите внимание: ласты POOL COLOUR SHORT маломерят на 2 размера!
                                </strong>
                            </div>
                            <br>
                        </div>
                        <?php } ?>

                        <?php if (!$isOneColorOneSize) { ?>
                            <?php if (Local::detectLocal() == Local::RU && in_array($v->product->category_id, [
                                342, 164, 1
                            ])) { ?>
                            <div class="clearfix">
                                <div style="margin: 0 !important;" class="alert alert-warning">
                                    <strong>Размеры гидрокостюмов для детей подбираются по размерной таблице для взрослых</strong> (гидрокостюмы для девочек подбираются по размерной сетке гидрокостюмов для женщин, гидрокостюмы для мальчиков подбираются по размерной сетке гидрокостюмов для мужчин).
                                    <?php if (in_array($v->product->id, [506, 507])) { ?>
                                        <strong>
                                            Обращаем внимание: BODYSHELL WOMEN & LIQUID WOMEN маломерят на 1 размер. Например, если по таблице у вас 170-XS, то вам нужен 170-S
                                        </strong>
                                    <?php } ?>

                                    <?php if (in_array($v->product->id, [503, 497])) { ?>
                                        <strong>
                                            Обращаем внимание: BODYSHELL man jammer и LIQUID man jammer маломерят на 1 размер.
                                        </strong>
                                    <?php } ?>
                                </div>
                                <br>
                            </div>
                            <?php }?>
                            <div class="row">
                                <b class="text-uppercase" style="font-size: 12px"><?= \Yii::t('app', 'Size') ?></b>
                                <div class="size-link"><a href="/about/info/sizes" target="_blank"><?= \Yii::t('app', 'Size-guide') ?></a></div>
                                <br>
                                <br>
                                <div class="clearfix">
                                    <?php foreach ($colors as $c_id => $c) {
                                        foreach ($c['sizes'] as $s_id => $size) {
                                            if ($v->color_id == $c_id) {
                                                $available = $c['sizes'][$s_id]['available'] ? '' : 'not-available';
                                                $active = $v->variant_id == $c['sizes'][$s_id]['sku'] ? 'active' : '';
                                                echo Html::tag('span', $size['label'], [
                                                    'data-loadview' => $c['sizes'][$s_id]['sku'],
                                                    'class' => "pointer label-size label label-default $available $active",
                                                ]);
                                            }
                                        }
                                    } ?>
                                </div>
                                <br>
                            </div>
                        <?php } ?>

                        <?= $v->renderAddToCartButton() ?>
                    </div>
                </div>

            </div>

            <div class="col-md-7 col-md-pull-5 col-xs-12 information">
                <h3><?= \Yii::t('app', 'Information') ?></h3>

                <div class="keys">
                    <p><?= \Yii::t('app', 'Vendor code') ?>: <?= $v->vendore_code ?></p>
                    <p><?= \Yii::t('app', 'Composition') ?>: <?= $model->getComposition() ?></p>
                </div>

                <div class="description">
                    <?php if ($model->id == 1272): ?>
<!--                        --><?//= $model->getDescription(true); ?>
                        <?php $bestReview = Testimonials::getBest($model); ?>

                        <?php if ($bestReview): ?>
                            <div class="best-review">
                                <div class="best-review-text row">
                                    <div class="fa fa-angle-double-left col-xs-1"></div>
                                    <div class="col-xs-11">
                                        <?= $bestReview->text ?>
                                        <div class="fa fa-angle-double-right"></div>
                                    </div>
                                </div>
                                <div class="best-review-reviewer">
                                    <div class="best-review-reviewer-initials pull-left">
                                        <?php
                                        $initials = '';
                                        foreach (explode(" ", $bestReview->name) as $word)
                                        {
                                            $initials .= mb_substr($word, 0, [1, [mb_internal_encoding("UTF-8")]]);
                                        }
                                        echo $initials;
                                        ?>
                                    </div>
                                    <div class="pull-left">
                                        <div class="best-review-reviewer-name">
                                            <?= $bestReview->name ?>
                                        </div>
                                        <?= Rating::widget(['rating' => $bestReview->rating]) ?>
                                        <br>
                                        <p>Общая оценка</p>
                                    </div>
                                    <a href="#reviews" class="pull-right">Все отзывы</a>
                                </div>
                            </div>
                        <?php endif ?>

                    <?php else: ?>
                        <?= $model->getDescription(); ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

