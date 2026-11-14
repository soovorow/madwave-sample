<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use common\models\Pricing;

/**
 * @var $ordersDataProvider \yii\data\ActiveDataProvider
 */
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Account'), 'url' => \yii\helpers\Url::to(['account/orders'])];

?>

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-3">
            <?= $this->render('_menu') ?>
        </div>
        <div class="col-xs-12 col-md-9">
            <h1 class="mt0 mb30"><?= Yii::t('app', 'My orders') ?></h1>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= Yii::t('app', 'Amount') ?></th>
                        <th><?= Yii::t('app', 'Created at') ?></th>
                        <th><?= Yii::t('app', 'Status') ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ordersDataProvider->models as $order) {
                            /**
                             * @var $order \common\models\Order
                             */?>
                            <tr>
                                <td><?= $order->getId() ?></td>
                                <td><?= $order->total ?> <?= \Yii::$app->params['currency'] ?></td>
                                <td><?= date('d-m-Y', $order->created_at) ?></td>
                                <td><?= Html::a(Yii::t('app', 'More'), $order->getUrl()) ?></td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            <?= LinkPager::widget([
                'pagination' => $ordersDataProvider->pagination
            ]) ?>
        </div>
    </div>
</div>