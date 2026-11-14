<?php

use common\components\GAEE\GAEE;
use common\components\AdmitAd;
use common\components\Criteo;
use common\components\multilocal\Local;
use common\models\products\Balance;
use common\models\products\Color;
use common\models\products\Price;
use common\models\products\Size;
use common\models\ProductVariant;

/* @var $this \yii\web\View */
/* @var $v ProductVariant */
/* @var $model \common\models\Product */

$v->measureDetailView();
$pv = ProductVariant::tableName();

if (Local::detectLocal() == Local::RU) {
    AdmitAd::setProductPage($v->id, $model->name, $model->category_id);
    Criteo::setProductPage($v->id);
}

if (\Yii::$app->language == 'ru-RU') {
    $colorLabel = 'name_ru';
} else {
    $colorLabel = 'name_en';
}

$skus = ProductVariant::find()
    ->select($pv.'.variant_id as sku')
    ->addSelect($pv.'.color_id')
    ->addSelect('c.'.$colorLabel.' as color_name')
    ->addSelect('s.size_id')
    ->addSelect('s.name as size_name')
    ->addSelect('b.quantity as quantity')
    ->where(['product_id' => $model->getId()])
    ->leftJoin(Color::tableName().' c', 'c.color_id = '.$pv.'.color_id')
    ->leftJoin(Size::tableName().' s', 's.size_id = '.$pv.'.size_id')
    ->leftJoin(Balance::tableName().' b', 'b.variant_id = '.$pv.'.variant_id and b.store_id = "'.Local::choose([
            Local::RU => '00005',
            Local::EU => '00049',
        ]).'"')
    ->innerJoin(Price::tableName(). ' p', 'p.variant_id = '.$pv.'.variant_id and p.price_rub > 0')
    ->asArray()
    ->all();

$colors = [];

foreach ($skus as $sku) {
    $colors[$sku['color_id']]['label'] = $sku['color_name'];
    $colors[$sku['color_id']]['sku'] = $sku['sku'];
    $colors[$sku['color_id']]['sizes'][$sku['size_id']]['label'] = $sku['size_name'];
    $colors[$sku['color_id']]['sizes'][$sku['size_id']]['available'] = $sku['quantity'] > 0;
    $colors[$sku['color_id']]['sizes'][$sku['size_id']]['sku'] = $sku['sku'];
}

$isOneColorOneSize = true;
foreach ($colors as $c_id => $c) {
    $available = false;
    foreach ($c['sizes'] as $s_id => $size) {
        if ($c['sizes'][$s_id]['available']) {
            $available = true;
        }
    }
    $colors[$c_id]['available'] = $available;

    if (count($c['sizes']) !== 1) {
        $isOneColorOneSize = false;
    }
}

$currency = \Yii::$app->params['currency']

?>

<div class="container">

    <div class="product-card-new">

        <?= \common\widgets\product\page\MarketingLabels::widget([
            'v' => $v,
            'model' => $model,
            'show_product_page_button' => isset($show_product_page_button) ? $show_product_page_button : false
        ]) ?>
        <div class="col-md-8 product-card-new-images">
            <div class="row">
                <div class="product-card-new-images-large">
                    <img id="product-card-new-images-large"
                         alt="<?= $v->product->globalCategory->getName(). ' ' . $v->name  ?>"
                         src="<?= $v->getImage(1, ProductVariant::IMG_ORIGINAL, true, true) ?>"/>
                </div>
                <div class="product-card-new-images-small">
                    <?php for ($i = 1; $i <= 4; $i++) {
                        $img = $v->getImage($i, ProductVariant::IMG_ORIGINAL);
                        if ($img) { ?>
                        <span class="product-card-new-images-small-image">
                            <?= $img ?>
                        </span>
                    <?php } } ?>
                </div>
            </div>
        </div>
        <div class="col-md-4 product-card-new-desc">
            <div class="product-card-new-name">
                <?= $model->name; ?>
            </div>
            <div class="product-card-new-keys">
            <span>
                <?= \Yii::t('app', 'Composition') ?>: <?= $model->getComposition() ?>
            </span>
                <span>
                <?= \Yii::t('app', 'Vendor code') ?>: <?= $v->vendore_code ?>
            </span>
            </div>
            <div class="product-card-new-price">
            <span class="product-card-new-price-old">
                <?php if ($v->salePrice): ?>
                    <span>
                        <?= number_format($v->oldPrice, 0, '.', ' ') . $currency ?>
                    </span>
                <?php elseif ($v->product->discount_card != 1): ?>
                    <?= number_format($v->price, 0, '.', ' ') . $currency ?>
                <?php else: ?>
                    &nbsp;
                <?php endif; ?>
            </span>
                <span class="product-card-new-price-new">
                <?php if ($v->salePrice || $v->product->discount_card == 1): ?>
                    <?= number_format($v->price, 0, '.', ' ') . $currency ?>
                <?php elseif ($v->product->discount_card != 1 && \Yii::$app->language == 'ru-RU'): ?>
                    <span class="color-primary">
                        <?= number_format($v->cardPrice, 0, '.', ' ') . $currency ?><i class="fa fa-credit-card"></i>
                    </span>
                <?php endif; ?>
            </span>
            </div>
            <hr>
            <div class="product-card-new-colors">
                <div class="product-card-new-colors-title">
                    <?= Yii::t('app', 'Available colors') ?>
                </div>
                <div class="product-card-new-colors-block">
                    <?php foreach ($colors as $c_id => $c): ?><?php $active = $v->color_id == $c_id; ?><div class="product-card-new-colors-color <?= $active ? 'active' : '' ?>" data-loadview="<?= $c['sku'] ?>"><span class="colorbox color--<?= $c_id ?> " style="width: 25px; height: 25px; top: 0; bottom: 0;"></span></div><?php endforeach ?>
                </div>
            </div>
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
            <?php if (count($colors[$v->color_id]['sizes']) > 1) { ?>

            <div class="product-card-new-sizes">
                <div class="product-card-new-sizes-title">
                    <?= Yii::t('app', 'Choose size') ?>
                    <span class="size-link"><a href="/about/info/sizes" target="_blank"><?= \Yii::t('app', 'Size-guide') ?></a></span>
                </div>
                    <div class="product-card-new-sizes-block">
                        <?php foreach ($colors[$v->color_id]['sizes'] as $s_id => $size): ?><?php $available = $size['available'] ? '' : 'not-available'; ?><?php $active = $v->variant_id == $size['sku'] ? 'active' : ''; ?><div class="product-card-new-sizes-size <?= $available ?> <?= $active ?>" data-loadview="<?= $size['sku'] ?>"><?= $size['label'] ?></div><?php endforeach ?>
                    </div>
            </div>
            <?php } ?>

            <div class="product-card-new-add-btn">
                <?= $v->renderAddToCartButton() ?>
            </div>
        </div>
    </div>
</div>

<?php //if (in_array($model->id, [501, 1497, 1498, 1493])): ?>
    <style>
        #description-spoiler {
            display: none;
        }
    </style>
    <?php
    $this->registerJs(<<<JS
    $('body').on('click', '.toggle-description-spoiler', function() {
        $('#description-spoiler').toggle();
        $('.toggle-description-spoiler').toggleClass('hidden')
    })
JS
)
    ?>
    <div class="description-morozov">
        <div class="container">
            <div class="col-md-8">
                <h3><?= \Yii::t('app', 'Information') ?></h3>
                <p><?= $model->getDescription(true) ?></p>
            </div>
            <div class="col-md-4">
                <?php if ($model->getFeaturesDescription()) { ?>
                <h3><?= Yii::t('app', 'Features') ?></h3>
                <?php foreach ($model->getFeaturesDescription() as $i => $feature) { ?>
                    <?php if (count($model->getFeaturesDescription()) > 3 && $i == 2) { ?>
                        <div class="toggle-description-spoiler" style="padding: 10px 0 20px 40px;">
                            <div class="btn btn-default">
                                <?= Yii::t('app', 'Show more') ?>
                            </div>
                        </div>
                        <div id="description-spoiler">
                    <?php } ?>

                    <div class="description-morozov-feature">
                        <div class="description-morozov-feature-icon">
                            <div class="fa fa-check"></div>
                        </div>
                        <p><?= $feature ?></p>
                    </div>

                    <?php if ($i > 2 && $i+1 == count($model->getFeaturesDescription())) { ?>
                        <div class="toggle-description-spoiler hidden" style="padding: 10px 0 20px 40px;">
                            <div class="btn btn-default">
                                <?= Yii::t('app', 'Show less') ?>
                            </div>
                        </div>
                        </div>
                    <?php } ?>
                <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
<?php //endif ?>
