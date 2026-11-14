<?php

use frontend\modules\checkout\eu\models\Checkout;
use frontend\modules\checkout\eu\models\delivery_methods\DeliveryMethod;
use frontend\modules\checkout\eu\models\pricing\Discounts;

$deliveryCost = DeliveryMethod::getDeliveryCost(true);
$cardDiscount = Discounts::getTotalCardDiscountSum(true);
$codeDiscount = Discounts::getTotalCodeDiscountSum(true);
$cardOnlyDiscount = Discounts::getCardOnlyDiscountSum(true);
$amount = \Yii::$app->cart->getCost();
?>


<table class="cart-check">
    <tbody>

    <tr>
        <td>Amount:</td>
        <td><?= number_format($amount / 124 * 100, 2) . \Yii::$app->params['currency'] ?></td>
    </tr>
    <tr>
        <td>Odd VAT:</td>
        <td><?= number_format($amount / 124 * 24, 2) . \Yii::$app->params['currency'] ?></td>
    </tr>

<!--    --><?php //if($cardDiscount) { ?><!--<tr><td>Скидка по карте:</td><td>---><?//= $cardDiscount ?><!--</td></tr>--><?php //} ?>

<!--    --><?php //if($codeDiscount) { ?><!--<tr><td>Скидка по купону:</td><td>---><?//= $codeDiscount ?><!--</td></tr>--><?php //} ?>

<!--    --><?php //if($cardOnlyDiscount) { ?><!--<tr><td>---><?//= \frontend\modules\checkout\models\pricing\CardOnly::DISCOUNT ?><!--% за оплату картой</td><td>---><?//= $cardOnlyDiscount ?><!--</td></tr>--><?php //} ?>

    <?php if ($deliveryCost) { ?>
        <?php if (DeliveryMethod::getDeliveryCost() == 'fuck') { ?>
            <tr>
                <td colspan="2">
                    <div class="alert alert-warning" style="
                        margin-bottom: 20px !important;
                        line-height: 1.2;
                        margin-top: 20px;">
                        <small><b>Attention, </b> if the cost of delivery by courier is not shown, a manager will specify when calling to confirm the order</small>
                    </div>
                </td>
            </tr>
        <?php } else { ?>
            <tr><td>Delivery cost</td><td id="delivery-price"><?= $deliveryCost ?></td></tr>
            <tr>
                <td>Delivery VAT</td>
                <td id="delivery-vat"><?= $deliveryCost / 100 * 24 ?><?= Yii::$app->params['currency'] ?></td>
            </tr>
        <?php } ?>
    <?php } ?>


    <tr>
        <td>Total:</td>
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
        if (!$v->salePrice && $v->product->discount_card != 1) {
            $discount += ($v->getOldPrice() - $v->getCardPrice()) * $v->getQuantity();
        }
    }

    ?>
<!--    <div class="exclaim">-->
<!--        <span class="a openBonusModal" href="--><?//= Url::to(['/site/signup']) ?><!--">-->
<!--            Зарегистрируйтесь</span>-->
<!--        и ваша выгода составит-->
<!--            --><?php //if ($discount > 0) { ?>
<!--                <b>--><?//= $discount ?><!--</b> рублей!-->
<!--            --><?php //} else { ?>
<!--                до --><?//= \Yii::$app->params['highlyDiscount'] ?><!-- на следующую покупку-->
<!--            --><?php //} ?>
<!--    </div>-->
<?php } ?>
