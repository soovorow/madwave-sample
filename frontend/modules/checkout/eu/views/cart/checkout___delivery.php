<?php

use frontend\modules\checkout\eu\models\delivery_methods\DeliveryMethod;
use yii\helpers\Html;

/**
 * @var $this \yii\web\View
 * @var $form \yii\widgets\ActiveForm
 * @var $model \frontend\modules\checkout\eu\models\Delivery;
 * @var $m \frontend\modules\checkout\eu\models\delivery_methods\DeliveryMethod
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
    function initPosti() {

    }
</script>

<div class="delivery-methods">
    <h4 class="divide">Shipping method</h4>
    <div class="mb30 clearfix">
        <?= $form->field($model, 'delivery_method')->radioList($delivery_methods, [
            'class' => 'delivery-radio padded',
            'item' => function ($index, $label, $name, $checked, $value) {
                return Html::tag('div', Html::radio($name, $checked, [
                    'label' => $label,
                    'value' => $value,
                    'data-target' => '#delivery_' . $value,
                    'data-delivery' => $value,
                ]), ['class' => 'radio']);
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