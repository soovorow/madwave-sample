<?php

use common\models\ProductVariant;
use yii\helpers\Html;
use yii\widgets\LinkPager;

/* @var $productsDataProvider \yii\data\ActiveDataProvider */

$request = \Yii::$app->request;

$keywords = Html::encode($request->get('keywords'));

$this->title = Yii::t('app', 'Search').': "'.$keywords.'" | Mad Wave';

?>

<div class="container">
    <h1 class="text-center mb30"><?= Yii::t('app', 'Search by "{query}": {n,plural, one{# product} other{# products}} was found', [
        'query' => Html::encode($keywords),
        'n' => $productsDataProvider->getTotalCount(),
    ]) ?></h1>

    <div class="clearfix">
        <?php if ($productsDataProvider->models) { ?>
            <div class="product_list">
                <?php
                $n = isset($_GET['page']) ? (($_GET['per-page'] * ($_GET['page'] - 1)) + 1) : 1;
                foreach ($productsDataProvider->models as $model) {
                    echo $model->preview('Поиск', $n);
                    $n++;
                } ?>
            </div>
        <?php } ?>
    </div>

    <div class="text-center">
        <?= LinkPager::widget([
            'pagination' => $productsDataProvider->pagination,
        ]);  ?>
    </div>
</div>
