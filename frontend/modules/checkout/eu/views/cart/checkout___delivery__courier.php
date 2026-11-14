<?php

/**
 * @var $form \yii\widgets\ActiveForm
 * @var $model \frontend\modules\checkout\eu\models\delivery_methods\MethodCourier;
 */

$reverse = !empty($model->locality)
?>

<h4 class="divide">Address delivery</h4>

<div class="row place-input" style="display: none">
    <div class="col-xs-12">
        <?= $form->field($model, '_autoComplete')->textInput([
            'class' => 'form-control rad0 autocomplete-palce',
            'placeholder' => ''
        ]) ?>
    </div>
</div>
<div class="row place-result" style="display: block">
    <div class="col-xs-12 p0">
        <div class="col-xs-12 col-md-9">
            <?= $form
                ->field($model, 'address')
                ->textInput(['class' => 'form-control rad0',
                ])
            ?>
        </div>
        <div class="col-xs-12 col-md-3">
            <?= $form
                ->field($model, 'postalCode')
                ->textInput(['class' => 'form-control rad0']) ?>
        </div>
    </div>

</div>
