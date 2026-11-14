<?php

use common\models\Order;
use frontend\modules\checkout\ru\models\Checkout;
use \frontend\modules\checkout\ru\models\PaymentTerminal;
use \frontend\modules\checkout\ru\models\Payment;

/**
 * @var $this yii\web\View
 */

$this->title = 'Покупка совершена! | Mad Wave';
$model = Order::findOne(Yii::$app->request->get('id'));

Checkout::measureCheckoutStep(3);

$v = $model->total;

$this->registerJs(<<<JS
    gtag('event', 'conversion', {
        'send_to': 'AW-941159415/-kfsCPiwsnkQ9-fjwAM',
        'value': $v,
        'currency': 'RUB',
        'transaction_id': ''
    });
JS
);

?>

<div class="container">
    <div class="row checkout-cart">

        <?= $this->render('_nav', ['active' => 3]); ?>

        <div class="text-center col-xs-12 col-md-8 col-md-push-2 col-lg-6 col-lg-push-3">

            <?php $payment = new Payment(); ?>
            <?php if ($model->status == $model::STATUS_NEW && $payment->getMethodByCode($model->payment_method)->type == 'terminal') { ?>

                <h2 class="complete__header">Покупка почти завершена!</h2>
                <p class="mb30 complete__message">
                    Номер вашего заказа <br>

                    <h2><?= $model->id ?></h2>

                    Сейчас вы будете перенаправлены на сайт платежной системы <br>

                    Если этого не произошло в течение 10 секунд, пожалуйста, нажмите на кнопку:
                </p>

                <?php $paymentTerminal = new PaymentTerminal(); ?>
                <p> <?= $paymentTerminal->widget($model) ?> </p>

                <?php $this->registerJs('setTimeout(function () {$(\'.terminal-form\').submit()}, 10000)'); ?>

            <?php } else { ?>
            <h2 class="complete__header">Покупка совершена!</h2>
            <p class="mb30 complete__message">
                Номер вашего заказа <br>

                <h2><?= $model->getId() ?></h2>

                Данные о доставке и условиях заказа отправлены вам на указанную почту, наш менеджер свяжется с вами
                в ближайшее время  для уточнения деталей
            </p>
            <p>
                <a href="/" class="btn btn-default btn-lg">Вернуться в магазин</a>
            </p>
            <?php } ?>
        </div>
    </div>
</div>


