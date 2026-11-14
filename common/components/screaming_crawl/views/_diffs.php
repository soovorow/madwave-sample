<?php

use common\components\screaming_crawl\models\CrawlA;
use common\components\screaming_crawl\models\CrawlB;

$query = CrawlA::find()
    ->innerJoin('crawl_b', '`crawl_b`.`CanonicalLinkElement1` = `crawl_a`.`CanonicalLinkElement1`')
    ->where('1')
    ->andWhere(['crawl_a.StatusCode' => 200])
    ->andWhere('crawl_a.Content LIKE "text/html%"')
    ->andWhere('crawl_a.CanonicalLinkElement1 <> ""')
    ->andWhere('crawl_a.'.$diff_field.' != crawl_b.'.$diff_field)
    ->groupBy('CanonicalLinkElement1')
;

$canonicalMatchs = new yii\data\ActiveDataProvider([
    'query' => $query,
    'pagination' => ['pageSize' => 10]
]);

?>

<div class="row">
    <div class="col-xs-12">
        <div class="pull-left">
            <?php $form = \yii\bootstrap\ActiveForm::begin(['method' => 'post']); ?>
            <?= \yii\helpers\Html::submitInput('export '.$diff_field , ['name' => 'export-diffs', 'class' => 'btn btn-warning']); ?>
            <?php \yii\bootstrap\ActiveForm::end() ?>
        </div>
    </div>
</div>

<?= $this->render('__comparing_table', [
    'title' => $diff_field.' diffs',
    'dataProvider' => $canonicalMatchs,
    'comparingModel' => CrawlB::className(),
    'comparingField' => 'CanonicalLinkElement1'
]) ?>
