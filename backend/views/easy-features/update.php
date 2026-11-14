<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = 'Update EasyFeature';
$this->params['breadcrumbs'][] = ['label' => 'EasyFeatures', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
