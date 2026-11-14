<?php

use common\models\products\Color;
use common\models\ProductVariant;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <div class="col-xs-6">
        <?php $form = ActiveForm::begin(); ?>

        <div class="well">
            <?= $model->url ?>
        </div>

        <?= $form->field($model, 'slug')->textInput() ?>
        <?= $form->field($model, 'url_slug_prefix')->textInput() ?>
        <?= $form->field($model, 'unique_url_path')->textInput() ?>
        <?= $form->field($model, 'target_color')
            ->dropDownList(ArrayHelper::map(Color::find()
                ->where(['color_id' => ProductVariant::find()->where(['product_id' => $model->product_id])->asArray(true)->all()])
                ->all(), 'color_id', 'name_ru'), [
                    'prompt' => 'Целевой цвет не выбран'
            ]) ?>

        <div class="form-group">
         <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
