<?php

use common\models\OrderReport;
use frontend\modules\checkout\ru\models\delivery_methods\MethodPickup;
use frontend\modules\checkout\ru\models\payment_methods\MethodCOD;
use frontend\modules\checkout\ru\models\payment_methods\MethodYandex;

function nf($d) {
    return number_format($d, 0, '.', ' ');
}

$count = OrderReport::getOrdersCount();
$total = OrderReport::getOrdersTotal();
$delivery = OrderReport::getOrdersDelivery();
$orderItems = OrderReport::getOrderItems();
$prices = OrderReport::getPrices($orderItems);
array_multisort($prices, SORT_DESC, $orderItems);
$totalCharge = OrderReport::getTotalCharge($orderItems);

?>
<style>
    .table {
        width: 100%; border: 1px solid #ddd;
        table-layout: fixed; border-collapse: collapse;
    }
    .table th, .table td {
        border: 1px solid #ddd; padding: 10px;
    }
</style>

<h1>Ежедневный отчет, <?= date('d M Y',  OrderReport::getFromTo()['from']) ?></h1>
<h2>Оплата</h2>
<table class="table table-bordered">
    <thead>
    <tr>
        <th>Кол-во</th>
        <th>Сумма без доставки</th>
        <th>~ Прибыль (wholesale)</th>
        <th>Стоимость доставки</th>
        <th>Средний чек</th>
        <th>Оплачено онлайн</th>
        <th>Оплата при получении</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?= $count ?></td>
        <td><?= nf($total - $delivery) ?> р.</td>
        <td><?= nf($total - $delivery - $totalCharge) ?> р.</td>
        <td><?= nf($delivery) ?> р.</td>
        <td><?= nf($total / $count) ?> р.</td>
        <td><?= OrderReport::orders()->andWhere(['payment_method' => (new MethodYandex())->getCode()])->count(); ?></td>
        <td><?= OrderReport::orders()->andWhere(['payment_method' => (new MethodCOD())->getCode()])->count();  ?></td>
    </tr>
    </tbody>
</table>
<br>

<h2>Доставка</h2>
<table class="table table-bordered">
    <thead>
    <tr>
        <th>Курьер DPD</th>
        <th>Курьер МСК</th>
        <th>Курьер СПБ</th>
        <th>Почта РФ</th>
        <th>Почта бесплатно</th>
        <th>Самовывоз</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?= OrderReport::orders()->andWhere(['delivery_method' => '"ТГ504249"'])->count(); ?></td>
        <td><?= OrderReport::orders()->andWhere(['delivery_method' => '"ТГ505467"'])->count(); ?></td>
        <td><?= OrderReport::orders()->andWhere(['delivery_method' => '"ТГ504248"'])->count(); ?></td>
        <td><?= OrderReport::orders()->andWhere(['delivery_method' => '"Ф0001789"'])->count();  ?></td>
        <td><?= OrderReport::orders()->andWhere(['delivery_method' => '"ТГ504646"'])->count();  ?></td>
        <td><?= OrderReport::orders()->andWhere(['delivery_method' => (new MethodPickup())->getCode()])->count();  ?></td>
    </tr>
    </tbody>
</table>
<br>

<h2>Скидки</h2>
<table class="table table-bordered">
    <thead>
    <tr>
        <th>Со скидкой по карте</th>
        <th>Со скидкой по купону</th>
        <th>Со скидкой по карте и купону</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?= OrderReport::orders()->andWhere(['>', 'client_discount', 0])->count(); ?></td>
        <td><?= OrderReport::orders()->andWhere(['>', 'coupon_discount', 0])->count();  ?></td>
        <td><?= OrderReport::orders()->andWhere(['>', 'client_discount', 0])->andWhere(['>', 'coupon_discount', 0])->count()?></td>
    </tr>
    </tbody>
</table>
<br>

<h2>Товары</h2>

<table class="table table-bordered">
    <thead>
    <tr>
        <th>Наименование</th>
        <th>Артикул</th>
        <th>Количество</th>
        <th>Доход</th>
        <th>Прибыль</th>
    </tr>
    </thead>
    <tbody>
    <?php


    foreach ($orderItems as $k => $oi) {
        /**
         * @var $v \common\models\ProductVariant
         */
        $v = \common\models\ProductVariant::findByVendorCode($k)->one();
        $charge = $oi['price'] - ($v->getPurchasingPrice(true) * $oi['quantity']);
        ?>
        <tr>
            <td><a href="<?= $v->getUrl() ?>"><?= $v->getName()?></a></td>
            <td><?= $k ?></td>
            <td><?= $oi['quantity'] ?></td>
            <td><?= nf($oi['price']) ?> р.</td>
            <td><?= nf($charge) ?> р.</td>
        </tr>
    <?php } ?>
    </tbody>
</table>