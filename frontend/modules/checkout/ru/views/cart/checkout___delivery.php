<?php

use yii\helpers\Html;

/**
 * @var $this \yii\web\View
 * @var $form \yii\widgets\ActiveForm
 * @var $model \frontend\modules\checkout\ru\models\Delivery;
 * @var $m \frontend\modules\checkout\ru\models\delivery_methods\DeliveryMethod
 */

$cases = '';
foreach ($model->method as $k => $m) {
    $cases .= 'case '.$k.':'.$m->getJsInitFunction().'();break;';
}
?>
<script>
    function initDelivery(k) {
        switch (k) {<?= $cases ?>}
        ajaxit('post', '/checkout/ajax/set-delivery-cost', $("#payment_type-contaienr"), {method: k})
            .done(function(response) {
                var obj = JSON.parse(response);
                $("#payment_type-contaienr").html(obj.check)
            });
    }
</script>

<div class="delivery-methods">
    <h4 class="divide">Выберите способ доставки</h4>
    <div class="mb30 clearfix">
        <?= $form->field($model, 'delivery_method')->radioList($delivery_methods, [
            'class' => 'delivery-radio padded clearfix',
            'item' => function ($index, $label, $name, $checked, $value) {
                $model = (new \frontend\modules\checkout\ru\models\Checkout())->_delivery;
                $checked = $model->delivery_method == $index ? true : false;
                return Html::tag('div', Html::radio($name, $checked, [
                    'label' => $label,
                    'value' => $value,
                    'data-target' => '#delivery_' . $value,
                    'data-delivery' => $value,
                ]), ['class' => 'radio clearfix col-md-6']);
            }
        ])->label(false) ?>
    </div>
    <div class="tab-content">
        <?php foreach ($model->method as $i => $m) {
            $class = '';
            if ($model->delivery_method == $i) {
                $this->registerJs($m->getJsInitFunction()."()");
                $class = 'active';
            }
            ?>
            <div id="delivery_<?= $i ?>" class="tab-pane <?= $class ?> schedule-pane">
                <?= $this->render($m->getView(), ['form' => $form,'model' => $m]) ?>
            </div>
        <?php } ?>
    </div>
</div>