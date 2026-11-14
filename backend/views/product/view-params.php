<?php

use yii\helpers\Html;


$this->title = 'View params';
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
?>

<? $form = \yii\widgets\ActiveForm::begin([
    'options' => [
        'enctype' => 'multipart/form-data'
    ]
]) ?>




<h1><?= Html::encode($this->title) ?> <?= Html::submitButton('save', ['class' => 'btn btn-xs']) ?></h1>
<div class="row">
    <div class="col-md-4">
        <table class="table">
            <tr>
                <td><?= Html::a($model->name, $model->url) ?></td>
                <td>
                    <?= Html::dropDownList('product_status', $model->status == 1 ? 2 : 1, [
                        '1' => 'Отображать',
                        '2' => 'Скрыть',
                    ], ['class'=>'form-control']) ?>
                    <?= Html::hiddenInput('product_id', $model->product_id) ?>
                </td>
            </tr>

            <? foreach ($variantsByColor as $v) { ?>
                <tr>
                    <td><?= Html::a($v->vendore_code, $v->url) ?></td>
                    <td>
                        <?= Html::dropDownList('sku['.$v->id.']', $v->status == 1 ? 2 : 1, [
                            '1' => 'Отображать',
                            '2' => 'Скрыть',
                        ], ['class'=>'form-control']) ?>
                    </td>
                </tr>
            <? } ?>
        </table>
    </div>
</div>

<? $form::end() ?>
