<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin([
    'id' => 'psbank-form',
    'action' => $action,
    'options' => [
        'style' => 'display: inline-block;',
        'class' => 'terminal-form'
    ]
]);
?>
    <input type="hidden" name="AMOUNT" value="<?= $amount ?>">
    <input type="hidden" name="CURRENCY" value="<?= $currency ?>">
    <input type="hidden" name="ORDER" value="<?= $order_id ?>">
    <input type="hidden" name="DESC" value="Mad Wave LTD">
    <input type="hidden" name="TERMINAL" value="<?= $terminal ?>">
    <input type="hidden" name="TRTYPE" value="<?= $trtype ?>">
    <input type="hidden" name="MERCH_NAME" value="<?= $merch_name ?>">
    <input type="hidden" name="MERCHANT" value="<?= $merchant ?>">
    <input type="hidden" name="EMAIL" value="<?= $email ?>">
    <input type="hidden" name="TIMESTAMP" value="<?= $timestamp ?>">
    <input type="hidden" name="NONCE" value="<?= $nonce ?>">
    <input type="hidden" name="BACKREF" value="<?= $backref ?>">
    <input type="hidden" name="P_SIGN" value="<?= $hmac ?>">
<?= Html::submitButton('Перейти к оплате', ['class' => 'btn btn-lg btn-danger', 'style' => 'margin-left: 10px;']) ?>
<?php $form::end(); ?>