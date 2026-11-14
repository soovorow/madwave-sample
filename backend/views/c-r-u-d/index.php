<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->controller->title;
$this->params['breadcrumbs'][] = $this->title;
if (Yii::$app->controller->id === 'pinned-items') {
    $dataProvider->setSort(['defaultOrder' => ['pos' => SORT_ASC]]);
}
?>

<style>
    .list-group-item.active a {
        color: #fff;
    }
</style>

<div class="<?= $this->title ?>-index row">

    <? if (Yii::$app->controller->hasMenu()) { ?>
        <div class="col-md-9">
    <?php } else { ?>
        <div class="col-xs-12">
    <?php } ?>

        <h1>
            <?= Html::encode($this->title) ?>
            <?= Html::a('Create', ['create'], ['class'=>'btn btn-success btn-sm']) ?>
        </h1>

        <div class="col-xs-12">
            <?= $this->render('_search', ['model' => $searchModel]) ?>
        </div>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => (new Yii::$app->controller->main_model)->columns,
        ]); ?>

    <? if (Yii::$app->controller->hasMenu()) { ?>
        </div>
        <div class="col-md-3"><br><?= Yii::$app->controller->menu; ?></div>
    <?php } else { ?>
        </div>
    <?php } ?>

</div>
