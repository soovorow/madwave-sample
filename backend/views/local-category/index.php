<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Local Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1 style="margin-bottom: 30px">
        <?= Html::encode($this->title) ?>

        <?= Html::a('Create Category', ['create'], ['class' => 'btn btn-success pull-right']) ?>

    </h1>

    <ul>

        <?php if ($dataProvider->models) {
            foreach ($dataProvider->models as $i => $model ) {
                $current = $dataProvider->models[$i];
                $before = $dataProvider->models[($i - 1)];
                $after = $dataProvider->models[($i + 1)];
                ?>
                <li>
                        <?= Html::a('<span class="glyphicon glyphicon-trash"></span>',
                            ['delete', 'id'=>$model->id], [
                                'title' => Yii::t('yii', 'Delete'),
                                'aria-label' => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                                'data-pjax' => '0',
                            ]); ?>
                    <strong><?= Html::a($model->title, ['update', 'id'=>$model->id]) ?></strong>
                    <small>
                        <?= Html::a('Добавить подкатегорию', ['create', 'id'=>$model->id]) ?>

                        <? if ($after) { ?>
                            <?= Html::a('Опустить', [
                                'reduce-weight',
                                'id'=>$current->id,
                                'before'=>$before->id,
                                'after'=>$after->id,
                            ]) ?>
                        <? } ?>
                        <? if ($before) { ?>
                            <?= Html::a('Поднять', [
                                'increase-weight',
                                'id'=>$current->id,
                                'before'=>$before->id,
                                'after'=>$after->id,
                            ]) ?>
                        <? } ?>
                    </small>
                </li>
                <ul style="margin-bottom: 15px; margin-top: 10px">
                    <?php $subdata = \common\models\LocalCategory::find()
                        ->where(['parent_id'=>$model->id])
                        ->orderBy('weight')
                        ->all();
                    foreach ($subdata as $ii => $submodel) {
                        $before = $subdata[($ii - 1)];
                        $after = $subdata[($ii + 1)];
                        $current = $subdata[$ii];
                        ?>
                        <li>
                                <?= Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                    ['delete', 'id'=>$submodel->id], [
                                        'title' => Yii::t('yii', 'Delete'),
                                        'aria-label' => Yii::t('yii', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]); ?>
                            <strong><?= Html::a($submodel->title, ['update', 'id'=>$submodel->id]) ?></strong>
                            <small>
                                <? if ($after) { ?>
                                    <?= Html::a('Опустить', [
                                        'reduce-weight',
                                        'id'=>$current->id,
                                        'before'=>$before->id,
                                        'after'=>$after->id,
                                    ]) ?>
                                <? } ?>
                                <? if ($before) { ?>
                                    <?= Html::a('Поднять', [
                                        'increase-weight',
                                        'id'=>$current->id,
                                        'before'=>$before->id,
                                        'after'=>$after->id,
                                    ]) ?>
                                <? } ?>
                            </small>
                        </li>
                    <?php } ?>
                </ul>
            <?php }
        } ?>
    </ul>

</div>
