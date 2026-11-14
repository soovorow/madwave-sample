<?php

use common\components\GAEE\GAEE;
use frontend\modules\checkout\eu\models\Checkout;
use frontend\widgets\BestSellers;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 */

$this->title = 'Cart | Mad Wave';
$cartIsEmpty = !count(\Yii::$app->cart->getPositions()) > 0;
Checkout::measureCheckoutStep(1);
?>
<div class="checkout-cart">

    <?php if ($cartIsEmpty) { ?>
        <div class="container">
            <h1 class="empty-cart__h1">Cart</h1>
            <section class="empty-cart">
                <div class="empty-cart__bag"></div>
                <div class="empty-cart__container text-center">
                    <h2>Is your cart empty? No problem!</h2>
                    <p>
                        If you are registered with our site and had items in your cart, <br>
                        then just sign in to see them
                    </p>
                    <a href="<?= Url::to(['/']) ?>" class="btn btn-danger">Continue shopping</a>
                </div>
            </section>
            <br>
            <br>
            <h2>Bestsellers</h2>
        </div>
    <?php } else { ?>
        <div class="container p0">
            <?= $this->render('_nav', ['active' => 1]); ?>
        </div>
        <div class="container p0">
            <div class="col-xs-12 col-md-8">
                <h2>ITEMS IN YOUR CART</h2>
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
                <h2 class="text-center">YOU MIGHT HAVE FORGOTTEN TO ADD TO CART</h2><br><br>
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
