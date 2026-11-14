<?php

/* @var $this \yii\web\View */
/* @var $page \common\models\Page */

use vova07\imperavi\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<div class="page-detail">
    <h1>Create new page</h1>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($page, 'title'); ?>
    <?= $form->field($page, 'meta_desc'); ?>
    <?= $form->field($page, 'slug'); ?>
    <?= $form->field($page, 'body')->widget(Widget::className(), [
        'settings' => [
            'lang' => 'ru',
            'minHeight' => 200,
            'imageManagerJson' => Url::to(['images-get']),
            'imageUpload' => Url::to(['image-upload']),
            'plugins' => [
//                'clips',
                'imagemanager',
            ],
        ]
    ]); ?>

    <?= Html::submitButton('Save', ['class' => 'btn btn-success']); ?>

    <?php ActiveForm::end(); ?>
</div>
