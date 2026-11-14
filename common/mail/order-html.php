<?php

/** @var $order \common\models\Order */
use common\components\multilocal\Local;
use frontend\modules\checkout\ru\models\pricing\PromoCode;

?>

<div>
    <h1>
        <?= Yii::t('app', 'Thank you for your order #{n} on madwave.ru!', [
            'n' => $order->getId()
        ]) ?>
    </h1>

    <?php if (\common\models\sale\March8::isEnabled() && Local::detectLocal() == Local::RU) { ?>
    <p>
        Обращаем ваше внимание! В связи с праздниками, заказы, оформленные с 7.03.2018 по 11.03.2018, начнут обрабатываться и отправляться с 12.03.2018. Просьба учитывать это при оформлении заказов.
    </p>
    <?php } ?>

    <h2><?= Yii::t('app', 'Customer details') ?>:</h2>
    <table style="width: 100%">
        <tbody>
        <tr><td><b><?= Yii::t('app', 'Name') ?></b></td><td><?= $order->getCustomerFullName() ?></td></tr>
        <tr><td><b><?= Yii::t('app', 'Phone') ?></b></td><td><?= $order->getCustomerPhone() ?></td></tr>
        <tr><td><b>E-mail</b></td><td><?= $order->getCustomerEmail() ?></td></tr>
        <tr><td><b><?= Yii::t('app', 'Delivery address') ?></b></td><td><?= $order->getDeliveryAddress() ?></td></tr>
        <tr><td><b><?= Yii::t('app', 'Delivery method') ?></b></td><td><?= $order->getDeliveryMethod() ?></td></tr>
        <tr><td><b><?= Yii::t('app', 'Payment method') ?></b></td><td><?= $order->getPaymentMethod() ?></td></tr>
        <tr><td><b><?= Yii::t('app', 'Comment about the order') ?></b></td><td><?= $order->customer_notes ?></td></tr>
        <?php if (!empty($order->coupon)) { ?>
            <tr><td><b><?= Yii::t('app', 'Promo Code') ?></b></td>
                <td>
                    <?= $order->coupon ?>
                    <?php if ($coupon = PromoCode::findOne(['code' => $order->coupon])) { ?>
                        <?= $coupon->title ?>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <br>

    <h2><?= Yii::t('app', 'Your order')?></h2>
    <table style="width: 100%">
        <thead>
        <tr>
            <th style="text-align: left; border-bottom: 1px solid #ccc;"><?= Yii::t('app', 'Product name') ?></th>
            <th style="text-align: left; border-bottom: 1px solid #ccc;"><?= Yii::t('app', 'Size') ?></th>
            <th style="text-align: left; border-bottom: 1px solid #ccc;"><?= Yii::t('app', 'Colour') ?></th>
            <th style="text-align: left; border-bottom: 1px solid #ccc;"><?= Yii::t('app', 'Price') ?></th>
            <th style="text-align: left; border-bottom: 1px solid #ccc;"><?= Yii::t('app', 'Discount percent') ?></th>
            <th style="text-align: left; border-bottom: 1px solid #ccc;"><?= Yii::t('app', 'Price with discount') ?></th>
            <th style="text-align: left; border-bottom: 1px solid #ccc;"><?= Yii::t('app', 'Quantity') ?></th>
            <th style="text-align: left; border-bottom: 1px solid #ccc;"><?= Yii::t('app', 'Amount') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        $items = $order->getOrderItems();
        $currency = Local::choose([
            Local::RU => ' руб.',
            Local::EU => ' eur.',
        ]);

        /** @var $product \common\models\OrderItem */
        foreach ($items as $product) {
            $cost = $product->sum / $product->quantity;
            ?>
            <tr>
                <td><b><?= $product->name ?></b><br><?= $product->vendore_code?></td>
                <td><?= $product->size ?></td>
                <td><?= $product->color ?></td>
                <td><?= $product->clearPrice ?> <?= $currency ?></td>
                <td>
                    <?php if ($product->clearPrice > $cost) {
                        $percent = $product->clearPrice / 100; ?>
                        <b style="color: red">-<?= ($product->clearPrice - $cost) / $percent .'%' ?></b>
                        <?php
                    } ?>
                </td>
                <td><?= $cost ?></td>
                <td><?= $product->quantity ?></td>
                <td>
                    &nbsp;<b><?= $product->sum ?> <?= $currency ?></b>
                    <br>
                    <? if ($product->discount > 0) { ?>
                        <span class="color-red">-<?= $product->discount ?> <?= $currency ?></span>
                    <? } ?>
                </td>
            <tr>
        <?php } ?>
        </tbody>
    </table>
    <br>

    <table style="width: 100%">
        <tbody>
        <tr>
            <td> <?= Yii::t('app', 'Subtotal') ?> </td>
            <td><?= $order->preparedSum ?> <?= $currency ?></td>
        </tr>
        <?php

        $discounts = $order->preparedSum + $order->delivery_price - $order->total;

        if ($discounts > 0) { ?>
            <tr>
                <td> <?= Yii::t('app', 'Total discounts') ?> </td>
                <td class="color-red">-<?=  $discounts  ?> <?= $currency ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td> <?= Yii::t('app', 'Delivery cost') ?> </td>
            <td><?= $order->delivery_price ?> <?= $currency ?></td>
        </tr>
        <tr>
            <td><h3><?= Yii::t('app', 'Total') ?></h3></td>
            <td><h3 class="color-red"><?=  $order->total ?> <?= $currency ?></h3></td>
        </tr>
        </tbody>
    </table>


    <p>
        <?= Yii::t('app', 'Your order will be processed and available at the selected address. Within one business day a Mad Wave
manager will contact you to confirm the order.') ?>
    </p>
    <p>
        <?= Yii::t('app', 'Once the order is available in the store, the seller will contact you!') ?>
    </p>
    <p>
        <?= Yii::t('app', 'If you have any questions, you can call toll free at <strong>+358942451310</strong> or
         request a call for more information on the status of your order.') ?>
    </p>

    <?php if ($order->delivery_method == '"ТГ504642"') { ?>
        <p>
            <strong>
                <?= Yii::t('app', 'Attention! Upon receipt of the order in the original retail network Mad Wave fitting is required, otherwise, we can not guarantee the exchange and return of the goods.') ?>
            </strong>
        </p>
    <?php }?>

    <br/>
    <?= Yii::t('app', 'Contact us in case of any questions!') ?><br/>
    <?= Yii::t('app', 'Sincerely, madwave.eu!') ?>
</div>


<tr>
    <td valign="top" id="templateBody"></td>
</tr>
