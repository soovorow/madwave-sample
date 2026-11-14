<?php

use yii\helpers\Html;


/* @var $this yii\web\View */

$this->title = 'Create Local Category';
$this->params['breadcrumbs'][] = ['label' => 'Local Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-xs-6">
    <div class="row">
        <div class="category-create">

            <h1><?= Html::encode($this->title) ?></h1>

            <?= $this->render('_form', [
                'model' => $model,
                'categories' => $categories,
            ]) ?>

        </div>

    </div>
</div>