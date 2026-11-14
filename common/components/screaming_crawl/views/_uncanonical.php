<?php

use common\components\screaming_crawl\models\CrawlA;
use common\components\screaming_crawl\models\CrawlB;

$uncanonicalA = new yii\data\ActiveDataProvider([
    'query' => CrawlA::find()
        ->where('1')
        ->andWhere(['StatusCode' => 200])
        ->andWhere('Content LIKE "text/html%"')
        ->andWhere('CanonicalLinkElement1 = ""')
    ,
    'pagination' => ['pageSize' => 10]
]);

$uncanonicalB = new yii\data\ActiveDataProvider([
    'query' => CrawlB::find()
        ->where('1')
        ->andWhere(['StatusCode' => 200])
        ->andWhere('Content LIKE "text/html%"')
        ->andWhere('CanonicalLinkElement1 = ""')
    ,
    'pagination' => ['pageSize' => 10]
]);

?>

<?= $this->render('__comparing_table', [
    'title' => 'Uncanonical A',
    'dataProvider' => $uncanonicalA,
    'comparingModel' => CrawlB::className(),
    'comparingField' => 'Address'
]) ?>

<?= $this->render('__comparing_table', [
    'title' => 'Uncanonical B',
    'dataProvider' => $uncanonicalB,
    'comparingModel' => CrawlA::className(),
    'comparingField' => 'Address'
]) ?>
