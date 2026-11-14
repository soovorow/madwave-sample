<?php

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Account'), 'url' => \yii\helpers\Url::to(['account/orders'])];

$this->params['breadcrumbs'][] = ['label' => 'Скидки и купоны'];

?>

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-3">
            <?= $this->render('_menu') ?>
        </div>
        <div class="col-xs-12 col-md-9">

<!--            <h1 class="mt0 mb30">Карта клиента</h1>-->

<!--            <div class="row mb60">-->
            <?php /*
            $uData = \common\models\UserData::findOne(\Yii::$app->user->identity->id);
            if (!empty($uData->client_card)) { ?>
                    <div class="col-xs-6">
                        <h2 class="color-primary" style="display: block; padding: 30px 30px 60px;border-radius: 6px;background: #000 url(../images/logo-white.svg) right top no-repeat;background-position: 90% 10%;">
                            <b style="display:block; font-size: 150%; margin-bottom: 40px">-10%</b>
                            <br>
                            <?= $uData->client_card ?>
                        </h2>
                    </div>
                    <div class="col-xs-6">
                        <br>
                        Карта любого значения предполагает возможность купить наш товар по специальной цене, указанной в карточке товара. Для получения скидки введите номер, указанный на карте, в поле "Карта клиента", а специальный промо-код, высылаемый вам по почте, в поле "Купон" на последнем этапе оформления заказа.
                    </div>
            <?php } else { ?>
                <div class="col-xs-12 mb30">
                    <h4>У вас еще нет карты? <?= \yii\helpers\Html::a('Получите прямо сейчас!', \yii\helpers\Url::to(['site/order-card'])) ?></h4>
                </div>
                <div class="col-xs-12">
                    <h4>У меня уже есть карта</h4>
                    <?php $form = \yii\bootstrap\ActiveForm::begin(['method' => 'post'])?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= \yii\helpers\Html::textInput('card', null, [
                                'class' => 'form-control'
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= \yii\helpers\Html::submitButton('Привязать', ['class' => 'btn btn-primary']) ?>
                        </div>
                    </div>

                    <?php $form::end() ?>
                </div>
            <?php } */ ?>
<!--            </div>-->

<!--            <h1>Доступные купоны</h1>-->


            <h1 class="mb30">История купонов</h1>

            <table class="table">
                <thead>
                <tr>
                    <th>Купон</th>
                    <th>Заказ</th>
                    <th>Сумма скидки</th>
                    <th>Дата использования</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($couponHistoryDataProvider->models as $ch) { ?>

                    <tr>
                        <td><?= $ch->coupon ?></td>
                        <td><?= \yii\helpers\Html::a('#' . $ch->id, $ch->url) ?></td>
                        <td><?= $ch->coupon_discount ?></td>
                        <td><?= date('d/m/Y', $ch->created_at) ?></td>
                    </tr>

                <?php } ?>

                </tbody>
            </table>

        </div>
    </div>
</div>