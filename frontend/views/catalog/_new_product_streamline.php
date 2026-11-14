<?php

use common\components\multilocal\Local;
use common\models\Product;
use common\models\ProductVariant;
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

$additionalWrapperClasses = $withoutWrap ? '' : 'col-md-6 col-lg-4'.($size == 2 ? 'col-lg-3' : '');

$additionalProductClasses = $hasAnyInStock ? '' : 'out-of-stock';

$view_url = \yii\helpers\Url::to(['/catalog/streamline-with-lens']);

$streamline_kit = ProductVariant::findOne(10021575);
$leftLens = Product::findOne(1358);
$rightLens = Product::findOne(1358);
$rlp = $rightLens->getVariantsBySizes(1)[0];

$llp = $leftLens->getVariantsBySizes(1)[0];
$price = $streamline_kit->price + $rlp->price + $llp->price;
$cardPrice = $streamline_kit->cardPrice + $rlp->cardPrice + $llp->cardPrice;
$old_price = $price;
$new_price = $cardPrice;
?>

<div class="catalog-border product-wrap <?= $additionalWrapperClasses ?>">

    <div class="product <?= $additionalProductClasses ?>">

        <div class="new-product-label">
            <?= Yii::t('app', 'NEW') ?>
        </div>

        <div class="image data-link" data-url="<?= $view_url ?>">
            <img src="<?= $v->getImage(1, $v::IMG_MEDIUM, true, true) ?>"
                 alt="<?= $product->getImageAlt()  ?>">
        </div>

        <a class="title" href="<?= $view_url ?>">
            Streamline Optical
        </a>

        <div class="category">
            <?= Yii::t('app', 'Prescription Goggles') ?>
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