<?php

use frontend\modules\checkout\ru\models\pricing\PromoCode;
use frontend\modules\checkout\ru\models\pricing\ClientCard;
use frontend\modules\checkout\ru\models\pricing\Discounts;
use yii\helpers\Html;

$session = \Yii::$app->session;
$client_card = ClientCard::fetchFromSession();
$promo_code = PromoCode::fetchFromSession();
?>

<h2>В корзине сейчас</h2>

<?= $this->render('_pricing_widget') ?>

<?= Html::a('Перейти к оформлению', '/checkout/cart/checkout', ['class' => 'btn btn-lg btn-danger w100 mb20']) ?>

<?php if (isset($error_msg) && strlen($error_msg) > 0) { ?>
    <div class="alert alert-warning mb20" style="margin-bottom: 20px !important;">
        <?= $error_msg ?>
    </div>
<?php  } ?>

<div class="clearfix mb20">

    <?php if (!\Yii::$app->user->isGuest) { ?>
        <?= Html::input('text', 'client_card', $client_card, [
            'id' => 'card-value',
            'class'=>'form-control rad0',
            'placeholder'=>'Карта «Madwave»',
            'style' => 'display: none',
            'disabled' => $client_card ? true : false
        ]) ?>
        <?= Html::tag('span',  $client_card ? 'Отменить скидку по карте' : 'Применить скидку по карте', [
            'id' => $client_card ? 'btn-remove-card' : 'btn-card',
            'class' => 'btn btn-default dark w100',
        ]) ?>
    <?php } else { ?>
        <div class="col-xs-8 p0 pr05">
            <?= Html::input('text', 'client_card', $client_card, [
                'id' => 'card-value',
                'class'=>'form-control rad0',
                'placeholder'=>'Карта «Madwave»',
                'disabled' => $client_card ? true : false
            ]) ?>
        </div>
        <div class="col-xs-4 p0 pl05">
            <?= Html::tag('span',  $client_card ? 'Убрать' : 'Добавить', [
                'id' => $client_card ? 'btn-remove-card' : 'btn-card',
                'class' => 'btn btn-default dark w100',
            ]) ?>
        </div>
    <?php } ?>
</div>

<div class="clearfix mb20">
    <div class="col-xs-8 p0 pr05">
        <?= Html::input('text', 'coupon', $promo_code->code, [
            'class'=>'form-control rad0',
            'id' => 'coupon-value',
            'placeholder'=>'Я знаю промо-код',
            'disabled' => $promo_code ? true : false
        ]) ?>
    </div>
    <div class="col-xs-4 p0 pl05">
        <?= Html::tag('span',  $promo_code ? 'Убрать' : 'Добавить', [
            'id' => $promo_code ? 'btn-remove-coupon' : 'btn-coupon',
            'class' => 'btn btn-default dark w100',
        ]) ?>
    </div>
</div>

<div class="well">
    <table class="w100">
        <tbody>
        <tr>
            <td class="icons">
                <div class="form-group field-startcheckout-card_only">
                    <label>
                        <input type="checkbox"
                               id="startcheckout-card_only"
                               name="StartCheckout[card_only]"
                                <?= Discounts::getCardOnlyDiscountSum() > 0 ? 'checked' : '' ?>
                               value="1">
                    </label>
                    <div class="help-block"></div>
                </div>
            </td>
            <td>
                <label for="startcheckout-card_only"
                       class="pointer"
                       style="font-weight: 500">
                    <?= \frontend\modules\checkout\ru\models\pricing\CardOnly::DISCOUNT ?>% скидка при оплате <br>
                    банковской картой онлайн
                </label>
            </td>
            <td class="icons"><i class="mastercard"></i><i class="visa"></i></td>
        </tr>
        </tbody>
    </table>
</div>
