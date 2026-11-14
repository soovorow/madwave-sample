<?php

use frontend\modules\checkout\ru\models\Checkout;
use yii\bootstrap\ActiveForm;
use frontend\modules\checkout\ru\models\Customer;
use frontend\modules\checkout\ru\models\Delivery;
use frontend\modules\checkout\ru\models\Payment;

/**
 * @var $this yii\web\View
 * @var $model \frontend\modules\checkout\ru\models\Checkout;
 */

$this->title = 'Оплата и доставка | Mad Wave';
Checkout::measureCheckoutStep(2);
?>

<div class="container">
    <div class="row checkout-cart">

        <?= $this->render('_nav', ['active' => 2]); ?>

        <?php $form = ActiveForm::begin([
                'id' => 'checkoutForm'
        ]) ?>

        <div class="col-xs-12 col-md-8">
            <h2>Информация по оплате и доставке</h2>

            <?= $this->render('checkout___customer', [
                'form' => $form,
                'model' => $model->_customer
            ]) ?>

            <?= Delivery::widget($model->_delivery, $form) ?>

            <div id="customer_note">
                <h4 class="divide" id="payment_methods_h4">Добавьте коментарий к заказу</h4>
                <?= $form->field($model, 'customer_notes')->textarea(['style' => 'resize: vertical;']) ?>
            </div>

            <?= Payment::widget($model->_payment, $form) ?>

        </div>

        <div class="col-xs-12 col-md-4 col-lg-3 col-lg-push-1 theiaContainer">

            <div id="payment_type-contaienr"
                 class="theiaStickySidebar ajax-loading">

                <?= $this->render('checkout__check'); ?>

            </div>
        </div>

        <?php $form::end() ?>
    </div>
</div>