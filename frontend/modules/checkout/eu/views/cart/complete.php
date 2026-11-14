<?php

use common\components\GAEE\GAEE;
use frontend\modules\checkout\eu\models\Checkout;
use frontend\modules\checkout\eu\models\payment_methods\MethodOnline;

/**
 * @var $this yii\web\View
 */


$this->title = 'Order complete! | Mad Wave';
$model = \common\models\Order::findOne(\Yii::$app->request->get('id'));

Checkout::measureCheckoutStep(3);
?>

<div class="container">
    <div class="row checkout-cart">

        <?= $this->render('_nav', ['active' => 3]); ?>

        <div class="text-center col-xs-12 col-md-8 col-md-push-2 col-lg-6 col-lg-push-3">

            <?php
            $methodOnline = (new MethodOnline())->getCode();
            if ($model->payment_method == $methodOnline) { ?>

                <h2 class="complete__header">Purchase nearly completed!</h2>
                <p class="mb30 complete__message">
                   Number your order <br>

                    <h2><?= $model->getId() ?></h2>.

                    You will now be redirected to Paytrail <br>

                    If nothing happens in 10 seconds, please, press the button:
                </p>
                <p>
                    <?= \frontend\widgets\Paytrail::widget(['order_id' => $model->getId()]) ?>
                </p>
            <?php } else { ?>
            <h2 class="complete__header">Purchase completed!</h2>
            <p class="mb30 complete__message">
                Order number <br>

                <h2><?= $model->getId() ?></h2>.

                The information about delivery and order terms has been sent to the email you have indicated. Our manager will contact you as soon as possible to clarify the details
                </p>
            <p>
                <a href="/" class="btn btn-default btn-lg">To return to the store</a>
            </p>
            <?php } ?>
        </div>
    </div>
</div>