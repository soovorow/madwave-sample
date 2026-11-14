<?php

use common\models\Pricing;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<h2>YOU ARE ORDERING</h2>

<table class="table cart-table cart-table-small">
    <tbody id="cart_slick" >
    <?php
    if ($products = \Yii::$app->cart->getPositions()) {
        foreach ($products as $product) {
            /** @var $product \common\models\ProductVariant */
            $data_product = $product->formatGaArray();
            $data_product['price'] = $product->price; ?>
            <tr class="row-small clearfix">
                <td class="product-cover">
                    <div>
                        <a href="<?= $product->getUrl(); ?>">
                            <img src="<?= $product->getImage(1, $product::IMG_SMALL, true) ?>" alt="<?= $product->getName() ?>">
                        </a>
                    </div>
                </td>
                <td>
                    <h5><b><?= Html::encode($product->getName()) ?></b></h5>
                    <?= $product->product->globalCategory->name_en ?><br>
                    <small>Quantity: <b><?= $quantity = $product->getQuantity(); ?></b></small>
                    <small class="pull-right"><b><?= Pricing::getItemSumWithDiscount($product) * $quantity ?> <?= \Yii::$app->params['currency'] ?></b></small>
                </td>
            </tr>
        <?php }
    } ?>
    </tbody>
</table>

<?= $this->render('_pricing_widget') ?>

<p><?= Html::submitButton('Confirm', ['class' => 'btn btn-lg btn-danger w100']) ?></p>
<p><?= Html::a('Back to cart', Url::to(['/checkout/cart/index']), ['class' => 'btn btn-lg btn-default w100']) ?></p>
