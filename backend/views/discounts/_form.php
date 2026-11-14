<?php

use frontend\modules\checkout\ru\models\pricing\PromoCode;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <div class="row">

        <div class="col-xs-6">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'title'            )->textInput() ?>

            <?= $form->field($model, 'how_many'         )->input('number', ['min' => 1]) ?>
            <?= $form->field($model, 'n_usage'          )->input('number', ['min' => 0])->label('Сколько раз использовать (0 - без ограничений)')  ?>


            <div class="row">
                <div class="col-xs-6">
                    <?= $form->field($model, 'type'             )->dropDownList(PromoCode::types(), ['prompt' => 'Не выбрано']) ?>

                </div>
                <div class="col-xs-6">
                    <?= $form->field($model, 'value'            )->textInput(['placeholder' => '% | SUM | vendor code '])->label('Значение (0 - без ограничений)') ?>
                </div>
            </div>

            <?= $form->field($model, 'group'  )->dropDownList(
                \yii\helpers\ArrayHelper::map(\common\models\LocalCategory::find()->all(), 'id', 'title'),
                ['prompt' => 'Для всех']) ?>

            <?= $form->field($model, 'allow_with_card')->checkbox() ?>

            <?= $form->field($model, 'excluding_type')->checkboxList([
                '1' => 'Спецпредложения',
            ]) ?>



            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Generate' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

    </div>

</div>
