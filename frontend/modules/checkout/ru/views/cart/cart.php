<?php

use common\components\multilocal\Local;
use frontend\modules\checkout\ru\models\Checkout;
use frontend\widgets\BestSellers;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 */

$this->title = 'Корзина | Mad Wave';
$cartIsEmpty = !count(\Yii::$app->cart->getPositions()) > 0;
Checkout::measureCheckoutStep(1);
?>
<div class="checkout-cart">

    <?php if ($cartIsEmpty) { ?>
        <div class="container">
            <h1 class="empty-cart__h1">Корзина</h1>
            <section class="empty-cart">
                <div class="empty-cart__bag"></div>
                <div class="empty-cart__container text-center">
                    <h2>В вашей корзине пусто? Это не страшно!</h2>
                    <p>
                        Если Вы зарегистрированы у нас на сайте и в вашей корзине были товары, <br>
                        то чтобы их увидеть необходимо авторизоваться
                    </p>
                    <a href="<?= Url::to(['/catalog/stock']) ?>" class="btn btn-danger">Перейти к покупкам</a>
                </div>
            </section>
            <br>
            <br>
            <h2>Хиты продаж</h2>
        </div>
    <?php } else { ?>
        <div class="container p0">
            <?= $this->render('_nav', ['active' => 1]); ?>
        </div>
        <div class="container p0">
            <div class="col-xs-12 col-md-8">
                <h2>Товары в вашей корзине</h2>
                <?php if (\common\models\sale\March8::isEnabled() && Local::detectLocal() == Local::RU) { ?>
                    <p  class="alert alert-warning" style="margin-bottom: 15px !important">
                        Обращаем ваше внимание! В связи с праздниками, заказы, оформленные с 7.03.2018 по 11.03.2018, начнут обрабатываться и отправляться с 12.03.2018. Просьба учитывать это при оформлении заказов.
                    </p>
                <?php } ?>
                <div id="cart-table-container" class="ajax-loading">
                    <?= $this->render('cart__table') ?>
                </div>
            </div>
            <div class="col-xs-12 col-md-4 col-lg-3 col-lg-push-1 theiaContainer">
                <div id="payment_type-contaienr" class="theiaStickySidebar ajax-loading clearfix">
                    <?= $this->render('cart__check') ?>
                </div>
            </div>
        </div>
        <div class="container p0">
            <div class="col-xs-12">
                <hr class="mb80">
                <h2 class="text-center">Возможно вы забыли положить в корзину</h2><br><br>
            </div>
        </div>
    <?php } ?>

    <div class="container p0">
        <div class="col-xs-12">
            <div class="row">
                <?= BestSellers::widget(['size' => 2, 'limit' => 4]); ?>
            </div>
        </div>
    </div>
</div>
