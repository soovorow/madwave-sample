
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php if($order->status == $order::STATUS_NEW): ?>
    <form action="https://payment.paytrail.com/" id="paytrail" style="display: inline-block;" method="post">
        <?php if (Yii::$app->controller->id == 'cart') {
            $this->registerJs('setTimeout(function () {$(\'#paytrail\').submit()}, 10000)');
        } ?>
        <input name="MERCHANT_ID" type="hidden" value="<?= $merchant_id ?>">
        <input name="AMOUNT" type="hidden" value="<?= $total ?>">
        <input name="ORDER_NUMBER" type="hidden" value="<?= $order_id ?>">
        <input name="REFERENCE_NUMBER" type="hidden" value="">
        <input name="ORDER_DESCRIPTION" type="hidden" value="">
        <input name="CURRENCY" type="hidden" value="EUR">
        <input name="RETURN_ADDRESS" type="hidden" value="<?= $success ?>">
        <input name="CANCEL_ADDRESS" type="hidden" value="<?= $fail ?>">
        <input name="PENDING_ADDRESS" type="hidden" value="">
        <input name="NOTIFY_ADDRESS" type="hidden" value="<?= $notify ?>">
        <input name="TYPE" type="hidden" value="S1">
        <input name="CULTURE" type="hidden" value="en_US">
        <input name="PRESELECTED_METHOD" type="hidden" value="">
        <input name="MODE" type="hidden" value="">
        <input name="VISIBLE_METHODS" type="hidden" value="">
        <input name="GROUP" type="hidden" value="">
        <input name="AUTHCODE" type="hidden" value="<?= $authcode ?>">
        <button type="submit" class="btn btn-lg btn-danger" style="margin-left: 10px;">Proceed to payment</button>
    </form>
<?php endif; ?>
<?php if($order->status == $order::STATUS_PAID): ?>
    <b>The order is paid!</b>
<?php endif; ?>

