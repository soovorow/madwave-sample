<?php

use common\components\GAEE\GAEE;
use yii\helpers\Url;


?>

<div class="checkout-nav">
    <div class="col-xs-12">

        <a href="/" class="back">
            <span class="arrow-left"></span>
            Back</a>

        <a href="<?= Url::to(['cart/clear']) ?>" class="pull-right hidden-xs trash">
            <i class="fa fa-trash-o"></i>
            CLEAR WHOLE CART</a>

        <hr>

    </div>
    <div class="col-xs-12">
        <ul>
            <li class="<?= $active == 1 ? 'active' : 'previous'  ?>">
                <a <?= $active == 1 ? '' : ' href="'.Url::to(['cart/index']).'"' ?>><span class="icon">
                        01</span><span class="hidden-xs">
                        COMPLETING THE ORDER</span></a></li>

            <li class="<?= $active == 2 ? 'active' : ($active == 3 ? 'previous' : '') ?>"><span class="icon">
                        02</span><span class="hidden-xs">
                        Payment and shipping</span></li>

            <li class="<?= $active == 3 ? 'active' : '' ?>"><span class="icon">
                        03</span><span class="hidden-xs">
                        PURCHASE COMPLETED</span></li>
        </ul>
    </div>
</div>