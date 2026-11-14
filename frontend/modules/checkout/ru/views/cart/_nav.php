<?php

use yii\helpers\Url;


?>

<div class="checkout-nav">
    <div class="col-xs-12">

        <a href="<?= Url::to(['/catalog/stock']) ?>" class="back">
            <span class="arrow-left"></span>
            Вернуться к покупкам</a>

        <a href="<?= Url::to(['cart/clear']) ?>" class="pull-right hidden-xs trash">
            <i class="fa fa-trash-o"></i>
            Очистить всю корзину</a>

        <hr>

    </div>
    <div class="col-xs-12">
        <ul>
            <li class="<?= $active == 1 ? 'active' : 'previous'  ?>">
                <a <?= $active == 1 ? '' : ' href="'.Url::to(['cart/index']).'"' ?>><span class="icon">
                        01</span><span class="hidden-xs">
                        Формирование заказа</span></a></li>

            <li class="<?= $active == 2 ? 'active' : ($active == 3 ? 'previous' : '') ?>"><span class="icon">
                        02</span><span class="hidden-xs">
                        Оплата и доставка</span></li>

            <li class="<?= $active == 3 ? 'active' : '' ?>"><span class="icon">
                        03</span><span class="hidden-xs">
                        Покупка совершена</span></li>
        </ul>
    </div>
</div>