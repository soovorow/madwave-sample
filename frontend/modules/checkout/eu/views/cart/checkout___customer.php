<?php

use common\models\Order;

?>
<div class="customer-fields">
    <h4 class="divide">CONFIRM YOUR INFORMATION</h4>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'country')->dropDownList(
                $countries = Order::getPostiCountries(),
                [
                    'prompt' => 'Select country',
                    'class' => 'form-control rad0'
                ]
            );
            ?>
        </div>
        <div class="col-md-4">
            <?= $form
                ->field($model, 'city')
                ->textInput(['class' => 'form-control rad0'])
            ?>
        </div>
        <div class="col-md-4">
            <?= $form
                ->field($model, 'email')
                ->textInput(['class' => 'form-control rad0']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form
                ->field($model, 'phone')
                ->textInput(['class' => 'form-control rad0'])
            ?>
        </div>
        <div class="col-md-4">
            <?= $form
                ->field($model, 'lastName')
                ->textInput(['class' => 'form-control rad0']) ?>
        </div>
        <div class="col-md-4">
            <?= $form
                ->field($model, 'firstName')
                ->textInput(['class' => 'form-control rad0']) ?>
        </div>
    </div>
</div>