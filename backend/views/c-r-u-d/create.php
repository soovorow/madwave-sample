<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = 'create '. Yii::$app->controller->title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->controller->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="crud-create">
    <h1 style="margin-bottom: 30px"><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
