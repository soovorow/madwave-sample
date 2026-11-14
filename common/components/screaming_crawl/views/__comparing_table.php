<?php $sc = new \common\components\screaming_crawl\ScreamingCrawl() ?>

<h2><?= $title ?> - <?= $dataProvider->totalCount ?></h2>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
        <tr>
            <th>CanonicalLinkElement1</th>
            <th>Title1</th>
            <th>Title1Length</th>
            <th>MetaDescription1</th>
            <th>MetaDescription1Length</th>
            <th>MetaKeyword1</th>
            <th>MetaKeywords1Length</th>
            <th>H11</th>
            <th>H12</th>
            <th>H11length</th>
            <th>H12length</th>
            <th>H21</th>
            <th>H22</th>
            <th>H21length</th>
            <th>H22length</th>
            <th>Size</th>
            <th>WordCount</th>
            <th>TextRatio</th>
            <th>Level</th>
            <th>Inlinks</th>
            <th>Outlinks</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->models as $model) {
            $B = $comparingModel::findOne([$comparingField => $model->$comparingField])
            ?>
            <tr>
                <td><span class="screaming-crawl-val"><?= $model->CanonicalLinkElement1 ?></span></td>
                <td><?= $sc->renderComparedValues('Title1', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('Title1Length', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('MetaDescription1', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('MetaDescription1Length', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('MetaKeyword1', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('MetaKeywords1Length', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('H11', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('H12', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('H11length', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('H12length', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('H21', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('H22', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('H21length', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('H22length', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('Size', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('WordCount', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('TextRatio', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('Level', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('Inlinks', $B, $model) ?></td>
                <td><?= $sc->renderComparedValues('Outlinks', $B, $model) ?></td>
            </tr>
        <? } ?>
        </tbody>
    </table>
</div>
<?= yii\widgets\LinkPager::widget([
    'pagination' => $dataProvider->pagination
]) ?>