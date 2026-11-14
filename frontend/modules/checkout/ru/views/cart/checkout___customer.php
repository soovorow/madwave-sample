<div class="customer-fields">
    <h4 class="divide">Подтвердите ваши данные</h4>
    <div class="row">
        <div class="col-md-4">
            <?= $form
                ->field($model, 'city')
                ->textInput(['class' => 'form-control rad0 location-city','disabled' => true])
                ->label('Город * <span class="pull-right call-city-picker pointer" style="color: #00b0cc;font-weight: bold;" data-reload="true">изменить</span>', ['style' => 'width: 100%'])
            ?>
        </div>
        <div class="col-md-4">
            <?= $form
                ->field($model, 'phone')
                ->textInput(['class' => ['class' => 'form-control rad0']])
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
                ->field($model, 'lastName')
                ->textInput(['class' => 'form-control rad0']) ?>
        </div>
        <div class="col-md-4">
            <?= $form
                ->field($model, 'firstName')
                ->textInput(['class' => 'form-control rad0']) ?>
        </div>
        <div class="col-md-4">
            <?= $form
                ->field($model, 'secondName')
                ->textInput(['class' => 'form-control rad0']) ?>
        </div>
    </div>
</div>