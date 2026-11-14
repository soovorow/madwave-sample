<?php

use common\models\Order;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->title = 'Редактировать заказ: ' . ' ' . $model->getId();
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактировать';
$model_id = $model->getId();
?>
<div class="order-update">

    <div class="order-form">

        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
            ga('create', 'UA-35217976-1', 'auto');
            ga('require', 'ec');
            ga('set', 'currencyCode', 'RUB');
        </script>

        <h1 style="margin-bottom: 30px">
            <?= Html::encode($this->title) ?>
            <?= Html::tag('span', 'ОПТОВЫЙ ЗАКАЗ', ['class' => 'btn btn-danger pull-right', 'onClick' => "ga('ec:setAction', 'refund', {'id': $model_id}); ga('send', 'event', 'Ecommerce', 'Refund', {'nonInteraction': 1});"]) ?>
            <?= Html::a('Выгрузить TXT', ['generate-txt', 'id' => $model->id], ['class' => 'btn btn-warning pull-right', 'style' => 'margin-right: 15px;']) ?>
        </h1>

        <div class="row">
            <div class="col-xs-12 col-md-5">

                <?php $form = ActiveForm::begin(); ?>

                <div class="row">
                    <div class="col-xs-4">
                        <?= $form->field($model, 'status')->dropDownList(Order::getOrderStatuses()) ?>
                    </div>
                    <div class="col-xs-4">
                        <?= $form->field($model, 'call_status')->dropDownList(Order::getCallStatuses(), [
                            'prompt' => 'Не выбрано'
                        ])?>
                    </div>
                    <div class="col-xs-4">
                        <?= $form->field($model, 'test_status')->dropDownList(Order::getTestStatuses(), [
                            'prompt' => 'Не выбрано'
                        ])?>
                    </div>

                    <div class="col-xs-12"><?= $form->field($model, 'track_code')->textInput() ?></div>

                    <div class="col-xs-12"><?= $form->field($model, 'customer_notes')->textarea() ?></div>

                    <div class="col-xs-4"><?= $form->field($model, 'customer_lastname')->textInput() ?></div>
                    <div class="col-xs-4"><?= $form->field($model, 'customer_firstname')->textInput() ?></div>
                    <div class="col-xs-4"><?= $form->field($model, 'customer_secondname')->textInput() ?></div>

                    <div class="col-xs-6"><?= $form->field($model, 'customer_phone')->textInput() ?></div>
                    <div class="col-xs-6"><?= $form->field($model, 'customer_email')->textInput() ?></div>

                    <div class="col-xs-6"><?= $form->field($model, 'delivery_method')->dropDownList([
                            '"ТГ507168"' => 'Posti Express Parcel',
                            '"ТГ504248"' => 'Курер СПБ',
                            '"ТГ505467"' => 'Курер МСК',
                            '"ТГ504249"' => 'Курер DPD',
                            '"Ф0001789"' => 'Почта РФ',
                            '"ТГ507103"' => 'Почта РФ 1й класс',
                            '"ТГ504646"' => 'Почта РФ бесплатно',
                            '"ТГ504642"' => 'Самовывоз',
                        ], ['prompt' => 'Not selected']) ?></div>
                    <div class="col-xs-6"><?= $form->field($model, 'delivery_price')->textInput(['readonly' => true]) ?></div>
                    <div class="col-xs-6"><?= $form->field($model, 'client_card')->textInput() ?></div>
                    <div class="col-xs-6"><?= $form->field($model, 'coupon')->textInput() ?></div>
                </div>


                <table class="table table-hover">
                    <tbody>
                    <tr><td><b>Адрес доставки</b></td><td><?= $model->getDeliveryAddress() ?></td></tr>
                    <tr><td><b>Способ оплаты</b></td><td><?= $model->getPaymentMethod() ?></td></tr>
                    </tbody>
                </table>

                <?= $form->field($model, 'total')->textInput(['readonly' => true]) ?>

                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-xs-12 col-md-7">


                <table class="table table-hover mb30">

                    <thead>
                    <tr>
                        <th>                      Наименование   </th>
                        <th>                      Размер         </th>
                        <th>                      Цвет           </th>
                        <th>                      Цена           </th>
                        <th class="text-center">  Количество     </th>
                        <th>                      Сумма          </th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php

                    $items = $model->getOrderItems();

                    $currency = \Yii::$app->params['currency'];

                    $totalSum = 0;

                    foreach ($items as $product) { ?>

                    <tr>

                        <td><b><?= $product->name ?></b><br><?= $product->vendore_code?></td>

                        <td><?= $product->size ?></td>

                        <td><?= $product->color ?></td>

                        <td>
                            <?= $product->clearPrice ?> <?= $currency ?>
                            <br>
                            <?php
                            $cost = $product->sum / $product->quantity;
                            if ($product->clearPrice > $cost) {
                                $percent = $product->clearPrice / 100; ?>
                                <b style="color: red">-<?= ($product->clearPrice - $cost) / $percent .'%' ?></b>
                                <?php
                            }
                            ?>
                        </td>

                        <td class="text-center">
                            <?= $product->quantity ?>
                        </td>

                        <td>

                            <?php $totalSum += $product->sum ?>
                            &nbsp;<b><?= $product->sum ?> <?= $currency ?></b>

                            <br>

                            <? if ($product->discount > 0) { ?>

                                    <span class="color-red">-<?= $product->discount ?> <?= $currency ?></span>

                            <? } ?>

                        </td>

                    <tr>

                        <?php } ?>

<!--                    <tr>-->
<!--                        <td>--><?//= $totalSum ?><!--</td>-->
<!--                    </tr>-->
                    </tbody>

                </table>

                <pre><?= $model->composeTxt() ?></pre>
            </div>
        </div>


    </div>

</div>
