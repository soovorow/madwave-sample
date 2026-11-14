<h2><?= $title ?> - <?= $dataProvider->totalCount ?></h2>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
        <tr>
            <th>CanonicalLinkElement1</th>
            <th>Address</th>
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
        <?php foreach ($dataProvider->models as $model) { ?>
            <tr>
                <td><span class="screaming-crawl-val"><?= $model->CanonicalLinkElement1 ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->Address ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->Title1 ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->Title1Length ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->MetaDescription1 ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->MetaDescription1Length ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->MetaKeyword1 ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->MetaKeywords1Length ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->H11 ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->H12 ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->H11length ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->H12length ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->H21 ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->H22 ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->H21length ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->H22length ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->Size ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->WordCount ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->TextRatio ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->Level ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->Inlinks ?></span></td>
                <td><span class="screaming-crawl-val"><?= $model->Outlinks ?></span></td>
            </tr>
        <? } ?>
        </tbody>
    </table>
</div>
<?= yii\widgets\LinkPager::widget([
    'pagination' => $dataProvider->pagination
]) ?>