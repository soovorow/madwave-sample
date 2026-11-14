<?php

use frontend\modules\checkout\ru\models\Checkout;
use frontend\modules\checkout\ru\models\delivery_methods\DeliveryMethod;
use frontend\modules\checkout\ru\models\pricing\Discounts;
use yii\helpers\Url;

$deliveryCost = DeliveryMethod::getDeliveryCost(true);
$cardDiscount = Discounts::getTotalCardDiscountSum(true);
$codeDiscount = Discounts::getTotalCodeDiscountSum(true);
$cardOnlyDiscount = Discounts::getCardOnlyDiscountSum(true);
?>


<table class="cart-check">
    <tbody>

    <tr>
        <td>Общая стоимость:</td>
        <td><?= \Yii::$app->cart->getCost() . \Yii::$app->params['currency'] ?></td>
    </tr>

    <?php if($cardDiscount) { ?><tr><td>Скидка по карте:</td><td>-<?= $cardDiscount ?></td></tr><?php } ?>

    <?php if($codeDiscount) { ?><tr><td>Скидка по купону:</td><td>-<?= $codeDiscount ?></td></tr><?php } ?>

    <?php if($cardOnlyDiscount) { ?><tr><td>-<?= \frontend\modules\checkout\ru\models\pricing\CardOnly::DISCOUNT ?>% за оплату картой</td><td>-<?= $cardOnlyDiscount ?></td></tr><?php } ?>

    <?php if ($deliveryCost) { ?>
        <?php if (DeliveryMethod::getDeliveryCost() == 'fuck') { ?>
            <tr>
                <td colspan="2">
                    <div class="alert alert-warning" style="
                        margin-bottom: 20px !important;
                        line-height: 1.2;
                        margin-top: 20px;">
                        <small><b>Обратите внимание, </b> если стоимость доставки курьером не будет указана, вам уточнит ее менеджер, при звонке о подтверждении заказа</small>
                    </div>
                </td>
            </tr>
        <?php } else { ?>
            <tr><td>Стоимость доставки</td><td id="delivery-price"><?= $deliveryCost ?></td></tr>
        <?php } ?>
    <?php } ?>


    <tr>
        <td>Итоговая стоимость:</td>
        <td>
            <h3>
                <?= Checkout::getTotalSum(true) ?>
            </h3>
        </td>
    </tr>

    </tbody>
</table>
<?php if (!$cardDiscount && \Yii::$app->user->isGuest) {

    $cart_items = \Yii::$app->cart->getPositions();

    $discount = 0;
    foreach ($cart_items as $v) {
        /** @var $v \common\models\ProductVariant */
        if (!$v->getSalePrice() && $v->product->discount_card != 1) {
            $discount += ($v->getOldPrice() - $v->getCardPrice()) * $v->getQuantity();
        }
    }

    ?>
    <div class="exclaim">
        <span class="a openBonusModal" href="<?= Url::to(['/site/signup']) ?>">
            Зарегистрируйтесь</span>
        и ваша выгода составит
            <?php if ($discount > 0) { ?>
                <b><?= $discount ?></b> рублей!
            <?php } else { ?>
                до <?= \Yii::$app->params['highlyDiscount'] ?> на следующую покупку
            <?php } ?>
    </div>
<?php } ?>
