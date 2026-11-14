<?php

/**
 * @var $form \yii\widgets\ActiveForm
 * @var $model \frontend\modules\checkout\ru\models\delivery_methods\MethodMail;
 */

$reverse = !empty($model->locality)
?>

<h4 class="divide">Выберите адрес доставки</h4>
<?php /*
<div class="row">
    <div class="col-xs-12 col-md-4">
        <?= $form
            ->field($model, 'lastName')
            ->textInput(['class' => 'form-control rad0']) ?></div>
    <div class="col-xs-12 col-md-4">
        <?= $form
        ->field($model, 'firstName')
            ->textInput(['class' => 'form-control rad0']) ?></div>
    <div class="col-xs-12 col-md-4">
        <?= $form
        ->field($model, 'secondName')
            ->textInput(['class' => 'form-control rad0']) ?></div>
</div>

 */ ?>
<!--<div class="row place-input" style="--><?//= $reverse ? 'display: none' : '' ?><!--">-->
<div class="row place-input" style="display: none">
    <div class="col-xs-12">
        <?= $form->field($model, '_autoComplete')->textInput([
            'class' => 'form-control rad0 autocomplete-palce',
            'placeholder' => ''
        ]) ?>
    </div>
</div>
<!--<div class="row place-result" style="--><?//= $reverse ? 'display: block' : '' ?><!--">-->
<div class="row place-result" style="display: block">
    <div class="col-xs-12 p0">
        <div class="col-xs-12 col-md-3">
            <?= $form
                ->field($model, 'locality')
                ->textInput(['class' => 'form-control rad0',
//                    'readonly' => true
                ]) ?></div>
        <div class="col-xs-12 col-md-9">
            <?= $form
                ->field($model, 'street')
                ->textInput(['class' => 'form-control rad0',
//                    'readonly' => true
                ])
//                ->label('Улица * <span class="pull-right place-edit pointer" style="color: #aaa" data-reload="true">изменить</span>', ['style' => 'width: 100%'])
            ?>
        </div>
    </div>
    <div class="col-xs-12 col-md-3">
        <?= $form
            ->field($model, 'house')
            ->textInput(['class' => 'form-control rad0']) ?></div>
    <div class="col-xs-12 col-md-3">
        <?= $form
            ->field($model, 'apartment')
            ->textInput(['class' => 'form-control rad0']) ?></div>
    <div class="col-xs-12 col-md-3">
        <?= $form
            ->field($model, 'flour')
            ->textInput(['class' => 'form-control rad0']) ?></div>
    <div class="col-xs-12 col-md-3">
        <?= $form
            ->field($model, 'postalCode')
            ->textInput(['class' => 'form-control rad0']) ?></div>
</div>
