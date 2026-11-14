<?php

use common\components\multilocal\Local;
use frontend\widgets\Rating;
use yii\helpers\Html;

/**
 * @var $v \common\models\ProductVariant
 * @var $size / is sets when we call rendering this file and depends of page where it will be render
 * @var $withoutWrap bool
 */

$product = $v->product;
$currency = \Yii::$app->params['currency'];

$gaProduct = $v->formatGaArray();

$hasAnyInStock = $v->hasAnyInStock();

if ($n != null) {
    $gaProduct['position'] = $n;
}

$addClass = 'col-lg-4';

if ($size == 2) {
    $addClass = 'col-lg-3';
}

$additionalWrapperClasses = $withoutWrap ? '' : 'col-md-6 '.$addClass;

$additionalProductClasses = $hasAnyInStock ? '' : 'out-of-stock';

$view_url = $v->getUrl();

$prices = $v->getPrices();

$old_price = $prices['old'];
$new_price = $prices['new'];
?>

<div class="catalog-border product-wrap <?= $additionalWrapperClasses ?>">

    <div class="product <?= $additionalProductClasses ?>">

        <?php if ($product->discount_card_percent > 0 && \common\models\sale\March8::isEnabled() && Local::detectLocal() == Local::RU): ?>
            <div class="new-product-label" style="background: #fd0030">
                <span style="font-size: 140%; font-style: italic; line-height: 115%; color: #fff; ">
                    -<?= $product->discount_card_percent ?>%
                </span>
            </div>
        <?php else: ?>

            <?php if ($product->label && $product->label->text) {
                $hex = $product->label->hex ? 'style="background: #'.strtolower($product->label->hex).' !important;"' : '';
                ?>
                <div class="new-product-label" <?= $hex ?>>
                    <?= $product->label->text ?>
                </div>
            <?php } ?>

        <?php endif; ?>

        <div class="image data-link" data-url="<?= $view_url ?>">
            <img src="<?= $v->getImage(1, $v::IMG_MEDIUM, true, true) ?>"
                 alt="<?= $product->getImageAlt()  ?>">
        </div>

        <a class="title" href="<?= $view_url ?>">
            <?= $product->name ?>
        </a>

        <div class="category">
            <?= $product->globalCategory->getName() ?>
        </div>

        <hr>

        <div class="clearfix">
            <?= Rating::widget([
                'rating' => $product->getRating(),
                'votesCount' => $product->getRatingVotes(),
                'enableDescription' => true
            ]) ?>
        </div>

        <?php if ($old_price): ?>
            <span class="old-price">
                <?= $old_price ?> <?= Yii::$app->params['currency'] ?>
            </span>
        <?php endif; ?>

        <?php if (Local::detectLocal() == Local::RU) { ?>
            <span class="new-price <?= $old_price ? 'color-primary' : '' ?>">
                <?= $new_price ?> <?= Yii::$app->params['currency'] ?> <i class="fa fa-credit-card" style="margin-left: 5px"></i>
            </span>
        <?php } ?>

        <?php
        /**
         * Following part of code was added to increase code-to-text ratio of catalog pages
         * And the disable array to avoid words spam of specific pages
         */
        if (!in_array($list, [
            'Детские ласты',
            'Женские гидрокостюмы',
            'Детские гидрокостюмы',
            'Шапочки для плавания'
        ])) {
            echo Html::tag('div', $product->getDescription(), ['class' => 'dropdown-menu']);
        } ?>

    </div>

</div>