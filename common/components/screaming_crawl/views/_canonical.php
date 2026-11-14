<?php

use common\components\screaming_crawl\models\CrawlA;
use common\components\screaming_crawl\models\CrawlB;

$canonicalMatchs = new yii\data\ActiveDataProvider([
    'query' => CrawlA::find()
        ->innerJoin('crawl_b', '`crawl_b`.`CanonicalLinkElement1` = `crawl_a`.`CanonicalLinkElement1`')
        ->where('1')
        ->andWhere(['crawl_a.StatusCode' => 200])
        ->andWhere('crawl_a.Content LIKE "text/html%"')
        ->andWhere('crawl_a.CanonicalLinkElement1 <> ""')
        ->groupBy('CanonicalLinkElement1')
    ,
    'pagination' => ['pageSize' => 10]
]);

$canonicalUniqueA = new yii\data\ActiveDataProvider([
    'query' => CrawlA::find()
       ->where('1')
        ->andWhere(['StatusCode' => 200])
        ->andWhere('`Content` LIKE "text/html%"')
        ->andWhere('CanonicalLinkElement1 <> ""')
        ->andWhere('NOT EXISTS (SELECT NULL FROM crawl_b b WHERE b.CanonicalLinkElement1 = crawl_a.CanonicalLinkElement1 AND StatusCode = 200 AND CanonicalLinkElement1 <> "")')
        ->groupBy('CanonicalLinkElement1')
    ,
    'pagination' => ['pageSize' => 10]
]);

$canonicalUniqueB = new yii\data\ActiveDataProvider([
    'query' => CrawlB::find()
        ->where('1')
        ->andWhere(['StatusCode' => 200])
        ->andWhere('`Content` LIKE "text/html%"')
        ->andWhere('CanonicalLinkElement1 <> ""')
        ->andWhere('NOT EXISTS (SELECT NULL FROM crawl_a a WHERE a.CanonicalLinkElement1 = crawl_b.CanonicalLinkElement1 AND StatusCode = 200 AND CanonicalLinkElement1 <> "")')
        ->groupBy('CanonicalLinkElement1')
    ,
    'pagination' => ['pageSize' => 10]
]);

?>

<?= $this->render('__comparing_table', [
    'title' => 'Canonical matches',
    'dataProvider' => $canonicalMatchs,
    'comparingModel' => CrawlB::className(),
    'comparingField' => 'CanonicalLinkElement1'
]) ?>


<?= $this->render('__simple_table', [
    'title' => 'Unique A',
    'dataProvider' => $canonicalUniqueA
]) ?>

<?= $this->render('__simple_table', [
    'title' => 'Unique B',
    'dataProvider' => $canonicalUniqueB
]) ?>
