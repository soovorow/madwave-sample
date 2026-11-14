<?php

use common\components\GAEE\GAEE;
use frontend\modules\checkout\eu\models\Checkout;
use yii\bootstrap\ActiveForm;
use frontend\modules\checkout\eu\models\Customer;
use frontend\modules\checkout\eu\models\Delivery;
use frontend\modules\checkout\eu\models\Payment;

/**
 * @var $this yii\web\View
 * @var $model \frontend\modules\checkout\eu\models\Checkout;
 */

$this->title = 'Payment and Delivery | Mad Wave';
Checkout::measureCheckoutStep(2);
?>

<div class="container">
    <div class="row checkout-cart">

        <?= $this->render('_nav', ['active' => 2]); ?>

        <?php $form = ActiveForm::begin([
                'id' => 'checkoutForm'
        ]) ?>

        <div class="col-xs-12 col-md-8">
            <h2>PAYMENT AND DELIEVERY INFORMATION</h2>

            <?= Customer::widget($model->_customer, $form) ?>

            <?= Delivery::widget($model->_delivery, $form) ?>

            <?= Payment::widget($model->_payment, $form) ?>

            <div id="customer_note">
                <h4 class="divide" id="payment_methods_h4">Add comment</h4>
                <?= $form->field($model, 'customer_notes')->textarea(['style' => 'resize: vertical;']) ?>
            </div>

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