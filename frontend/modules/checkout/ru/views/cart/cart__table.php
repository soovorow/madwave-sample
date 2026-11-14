<?php

/**
 * @var $this yii\web\View
 * @var $products common\models\ProductVariant[]
 */

use common\models\Pricing;
use common\components\AdmitAd;
use common\components\Criteo;
use common\components\multilocal\Local;
use frontend\modules\checkout\ru\models\pricing\PromoCode;
use yii\helpers\Html;
use yii\helpers\Json;

$products = \Yii::$app->cart->getPositions();

if (Local::detectLocal() == Local::RU) {
    AdmitAd::setCartPage($products);
    Criteo::setCartPage($products);
}

if ($products) { ?>

    <table class="table cart-table">
        <thead>
        <tr>
            <th colspan="3">Наименование</th>
            <th class="hidden-xs">Размер</th>
            <th>Количество</th>
            <th class="hidden-xs">Цена</th>
            <th>Общая стоимость</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $currency = \Yii::$app->params['currency'];

        /** @var $coupon \frontend\modules\checkout\ru\models\pricing\PromoCode; */
        $coupon = PromoCode::fetchFromSession();
        if ($coupon) {
            $is_gift_available = $coupon->type == $coupon::TYPE_GIFT;
            $limit = $coupon->type === $coupon::TYPE_VOUCHER ? $coupon->value : false;
        }

        foreach ($products as $product) {
            /** @var $product \common\models\ProductVariant */

            $data_product = $product->formatGaArray();
            $data_product['price'] = $product->price;
            $product_name = $product->getName();
            $quantity = $product->getQuantity();
            $items = [];
            for ($i = 1; $i <= 200; $i++) $items[$i] = $i;
            ?>
            <tr class="clearfix">
                <td class="product-cover">
                    <div>
                        <a href="<?= $product->getUrl(); ?>">
                            <img src="<?= $product->getImage(1, $product::IMG_SMALL, true) ?>"
                                 alt="<?= $product_name ?>">
                        </a>
                    </div>
                </td>
                <td class="name">
                    <h5><b><?= $product_name ?></b></h5>
                    <?= $product->product->globalCategory->getName() ?><br>
                    <small>Артикул: <?= $product->vendore_code ?></small>
                </td>
                <td class="size vm hidden-xs">
                    <?= $product->size->name ?>
                </td>
                <td class="vm count">
                    <?= Html::dropDownList('quantity', $quantity, $items, [
                            'data-sku' => $product->id,
                            'class' => 'cart-table__quantity-changer'
                    ]) ?>
                </td>
                <td class="vm price hidden-xs">
                    <?php $sumWithDiscount = Pricing::getItemSumWithDiscount($product); ?>
                    <span class="<?= Pricing::getItemSumClass($product) ?>">
                        <b><?= $sumWithDiscount ?> <?= $currency ?></b>
                    </span>
                    <br>
                    <?php if ($sumWithDiscount <> $product->oldPrice) { ?>
                        <span style="text-decoration: line-through"><?= $product->oldPrice ?> <?= $currency ?></span>
                    <?php } ?>
                </td>
                <td class="vm cost">
                    <span>
                        <?= $sumWithDiscount * $product->getQuantity() ?>
                    </span> <?= \Yii::$app->params['currency'] ?>
                        <br>
                        <?php if ($coupon) {
                            $_discount = $coupon->getDiscount($product, $limit, $is_gift_available);
                            $limit = $_discount['limit'];
                            $is_gift_available = $_discount['is_gift_available'];
                            if ($_discount['discount'] > 0) { ?>
                                <span class="color-red">-<?= $_discount['discount'] ?> <?= $currency ?></span>

                            <?php }
                        } ?>
                </td>
                <td class="vm action">
                    <?= Html::a(
                        '<i class="fa fa-trash-o"></i>',
                        ['cart/remove', 'id' => $product->getId()],
                        [
                            'class' => 'btn-ga-cart-action',
                            'data-product' => Json::encode($data_product),
                            'data-action' => 'remove',
                            'data-quantity' => $quantity
                        ])?>
                </td>
            </tr>
            <?php if (!$product->hasInStock()) { ?>
                <tr>
                    <td colspan="8">
                        <div class="alert alert-warning" style="margin-bottom: 0 !important; margin-top: -22px">
                        Обратите внимание! Товара нет в наличии, при переходе не следующий шаг, он автоматически будет удален из вашей коризны
                        </div>
                    </td>
                </tr>
            <?php } ?>

        <?php } ?>

        </tbody>
    </table>
<?php } else {
    // TODO empty state
} ?>
