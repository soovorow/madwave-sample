<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model \yii\db\ActiveRecord */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">

    <div class="col-xs-6">

        <?php $form = ActiveForm::begin();

        foreach ($model->formAttributes() as $attribute => $params) {
            if ($params['type'] == 'textInput') {
                echo $form->field($model, $attribute)->textInput();
            }
            if ($params['type'] == 'textarea') {
                echo $form->field($model, $attribute)->textarea();
                echo \vova07\imperavi\Widget::widget([
                    'selector' => '#'.strtolower($model->formName()).'-'.$attribute,
                    'settings' => [
                        'lang' => 'ru',
                        'minHeight' => 200,
                        'imageManagerJson' => Url::to(['images-get']),
                        'imageUpload' => Url::to(['image-upload']),
                        'plugins' => [
//                            'clips',
                            'imagemanager',
                        ],
                    ]
                ]);
            }
            if ($params['type'] == 'dropdown') {
                echo $form->field($model, $attribute)->dropDownList($params['items'], ['prompt' => 'select...']);
            }
            if ($params['type'] == 'fileInput') {
                echo $form->field($model, $attribute)->fileInput();
            }
            if ($params['type'] == 'reCaptcha') {
                echo $form->field($model, $attribute)->widget(
                    \himiklab\yii2\recaptcha\ReCaptcha::className()
                );
            }
            if ($params['type'] == 'action') {
                echo Html::a($params['name'], $params['url'], [
                    'class' => 'clearfix',
                    'style' => 'display: block; margin-bottom: 15px'
                ]);
            }
        } ?>

        <div class="form-group">
         <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
