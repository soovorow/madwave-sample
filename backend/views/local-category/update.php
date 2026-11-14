<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LocalCategory */

$this->title = 'Update Category: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title.' - update';
?>
<div class="category-update">

    <h1 style="padding: 30px 0 60px 0"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $categories,
    ]) ?>

</div>
