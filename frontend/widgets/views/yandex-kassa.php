<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin([
    'id' => 'yandex-form',
    'action' => 'https://money.yandex.ru/eshop.xml',
    'options' => [
            'style' => 'display: inline-block;',
            'class' => 'terminal-form',
    ]
]);

?>
    <input type="hidden" name="shopId" value="54552">
    <input type="hidden" name="scid" value="53126">
    <input type="hidden" name="orderNumber" value="<?= $order_id ?>">
    <input type="hidden" name="shopSuccessURL" value="<?= $success ?>">
    <input type="hidden" name="shopFailURL" value="<?= $fail ?>">
    <input type="hidden" name="shopDefaultUrl" value="<?= $default ?>">
    <input type="hidden" name="sum" value="<?= $total ?>">
    <input type="hidden" name="customerNumber" value="<?= $user ?>">
<?= Html::submitButton('Перейти к оплате', ['class' => 'btn btn-lg btn-danger', 'style' => 'margin-left: 10px;']) ?>

<?php $form::end(); ?>


