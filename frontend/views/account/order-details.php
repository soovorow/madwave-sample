<?php

/**
 * @var $model \common\models\Order
 */

use common\components\multilocal\Local;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Account'), 'url' => \yii\helpers\Url::to(['account/orders'])];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Order'). ' #'.$model->id];
?>

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-3">
            <?= $this->render('_menu') ?>
        </div>
        <div class="col-xs-12 col-md-9">
            <h1 class="mt0 mb30"><?= Yii::t('app', 'Your order') ?> #<?= $model->getId() ?></h1>

            <table class="table table-hover">
                <tbody>
                <tr><td><b><?= Yii::t('app', 'Name') ?></b></td><td><?= $model->getCustomerFullName() ?></td></tr>
                <tr><td><b><?= Yii::t('app', 'Phone') ?></b></td><td><?= $model->getCustomerPhone() ?></td></tr>
                <tr><td><b>E-mail</b></td><td><?= $model->getCustomerEmail() ?></td></tr>
                <tr><td><b><?= Yii::t('app', 'Delivery address') ?></b></td><td><?= $model->getDeliveryAddress() ?></td></tr>
                <tr><td><b><?= Yii::t('app', 'Delivery method') ?></b></td><td><?= $model->getDeliveryMethod() ?></td></tr>
                <tr><td><b><?= Yii::t('app', 'Payment method') ?></b></td><td><?= $model->getPaymentMethod() ?></td></tr>
                <?php if (!empty($model->coupon)) { ?>
                    <tr><td><b><?= Yii::t('app', 'Promo Code') ?></b></td><td><?= $model->coupon ?></td></tr>
                <?php } ?>
                <?php if (!empty($model->client_card)) { ?>
                    <tr><td><b><?= Yii::t('app', 'Client card') ?></b></td><td><?= $model->client_card ?></td></tr>
                <?php } ?>
                </tbody>
            </table>
            <br>
            <table class="table table-hover mb30">
                <thead>
                <tr>
                    <th>                      <?= Yii::t('app', 'Product name') ?>   </th>
                    <th>                      <?= Yii::t('app', 'Size') ?>         </th>
                    <th>                      <?= Yii::t('app', 'Color') ?>           </th>
                    <th>                      <?= Yii::t('app', 'Price') ?>           </th>
                    <th class="text-center">  <?= Yii::t('app', 'Quantity') ?>     </th>
                    <th>                      <?= Yii::t('app', 'Amount') ?>          </th>
                </tr>
                </thead>
                <tbody>
                <?php
                $items = $model->getOrderItems();
                $currency = \Yii::$app->params['currency'];
                foreach ($items as $product) { ?>
                <tr>
                    <td>
                        <b><?= $product->name ?></b>
                        <br><?= $product->vendore_code?>
                    </td>
                    <td><?= $product->size ?></td>
                    <td><?= $product->color ?></td>
                    <td><?= $product->clearPrice ?> <?= $currency ?></td>
                    <td class="text-center">
                        <?= $product->quantity ?>
                    </td>
                    <td>
                        &nbsp;<b><?= $product->sum ?> <?= $currency ?></b>
                        <br>
                        <?php if ($product->discount > 0) { ?>
                            <span class="color-red">-<?= $product->discount ?> <?= $currency ?></span>
                        <?php } ?>
                    </td>
                <tr>
                    <?php } ?>
                </tbody>
            </table>
            <table class="table table-hover table-first-td-bold table-first-td-60-per">
                <tbody>
                <tr>
                    <td> <?= Yii::t('app', 'Subtotal') ?> </td>
                    <td><?= $model->getClearPrice() ?> <?= $currency ?></td>
                </tr>
                <tr>
                    <td> <?= Yii::t('app', 'Delivery cost') ?> </td>
                    <td><?= $model->delivery_price ?> <?= $currency ?></td>
                </tr>
                <?php
                if ($model->total_discount > 0) { ?>
                    <tr>
                        <td> <?= Yii::t('app', 'Total discounts') ?> </td>
                        <td class="color-red">-<?=  $model->total_discount  ?> <?= $currency ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><h3><?= Yii::t('app', 'Total') ?></h3></td>
                    <td><h3 class="color-red"><?=  $model->total ?> <?= $currency ?></h3></td>
                </tr>
                </tbody>
            </table>

            <?php

            $isPaymentAvailable = Local::detectLocal() == Local::RU
                ? time() < strtotime('+1 day', $model->created_at)
                : true;

            if ($model->status == $model::STATUS_NEW && $isPaymentAvailable) { ?>
                <div id="order-details-terminals">

                    <h2><?= Yii::t('app', 'Pay online') ?></h2>
                    <?php

                    if (Local::detectLocal() == Local::RU) {
                        $payment = new \frontend\modules\checkout\ru\models\Payment();
                    } else {
                        $payment = new \frontend\modules\checkout\eu\models\Payment();
                    }

                    ?>
                    <?php foreach ($payment->_payment_methods as $key => $method): ?>
                        <?php if ($method->type == 'terminal'): ?>

                            <div class="radio">
                                <label class="payment_label">
                                    <input type="radio"
                                           name="payment"
                                           class="payment_input"
                                           value="<?= $method->widgetClass ?>"
                                           onchange="changeTerminal()"
                                           <?= (!$key)?'checked':'' ?>
                                    >
                                    <?= $method->getDescription() ?>
                                </label>
                            </div>
                        <?php endif ?>
                    <?php endforeach ?>

                    <?php
                    foreach ($payment->_payment_methods as $method) {
                        if ($method->type == 'terminal') {
                            $classWidget = '\\frontend\widgets\\' . $method->widgetClass;

                            echo '<div class="payment_button '.$method->widgetClass.'">
                            ' . $classWidget::widget(['order_id' => $model->getId()]) . '</div>';
                        }
                    }?>

                </div>

                <script>
                    changeTerminal();
                    function changeTerminal() {

                        var buttonDivHide = document.getElementsByClassName("payment_button");
                        var inputs = document.getElementsByClassName("payment_input");

                        for(var i = 0; i < buttonDivHide.length; i++) {
                            buttonDivHide[i].style.display = "none";
                        }

                        for (var i=0, len=inputs.length; i<len; ++i) {
                            if (inputs[i].checked) {
                                document.getElementsByClassName(inputs[i].value)[0].style.display = "block";
                            }
                        }
                    }
                </script>

            <?php } ?>
        </div>
    </div>
</div>