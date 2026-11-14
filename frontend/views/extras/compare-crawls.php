<?php
$connection = Yii::$app->getDb();

$leftUnique = $connection->createCommand('
SELECT crawl_a.* , crawl_b.* 
FROM crawl_a INNER JOIN crawl_b ON crawl_a.CanonicalLinkElement1 = crawl_b.CanonicalLinkElement1
WHERE crawl_a.CanonicalLinkElement1 <> ""
AND crawl_a.Content LIKE "%text/html%"
GROUP BY crawl_a.CanonicalLinkElement1 
');
$result = $leftUnique->queryAll();

$tds = [
    'id',
    'Address',
    'Content',
    'StatusCode',
//    'Status',
    'Title1',
    'Title1Length',
//    'Title1PixelWidth',
    'MetaDescription1',
    'MetaDescription1Length',
//    'MetaDescription1PixelWidth',
    'MetaKeyword1',
    'MetaKeywords1Length',
    'H11',
    'H12',
    'H11length',
    'H12length',
//    'H21',
//    'H22',
//    'H21length',
//    'H22length',
//    'MetaRobots1',
//    'MetaRefresh1',
    'CanonicalLinkElement1',
    'Size',
    'WordCount',
    'TextRatio',
    'Level',
    'Inlinks',
    'Outlinks',
    'ExternalOutlinks',
//    'Hash',
//    'ResponseTime',
//    'LastModified',
    'RedirectURI',
]

?>

<h1>Совпадений <?= count($result) ?></h1> <br>

<div class="table-responsive">

    <table class="table">
        <thead>
        <tr>
            <?php foreach ($tds as $td) { ?>
                <th><?= $td ?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($result as $r) { ?>
            <tr>
                <?php foreach ($tds as $td) { ?>
                    <td><?= $r[$td] ?></td>
                <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>