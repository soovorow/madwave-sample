<?php

$this->registerJs(' $(".screaming-crawl-val").click(function () {
    prompt("", $(this).text());
}) ');

$tds = [
    'CanonicalLinkElement1',
    'StatusCode',
//    'Address',
    'Title1',
    'Title1Length',
    'MetaDescription1',
    'MetaDescription1Length',
//    'MetaKeyword1',
//    'MetaKeywords1Length',
    'H11',
//    'H12',
//    'H11length',
//    'H12length',
//    'H21',
//    'H22',
//    'H21length',
//    'H22length',
//    'Size',
//    'WordCount',
//    'TextRatio',
//    'Level',
//    'Inlinks',
//    'Outlinks',
];

//$fB = Yii::getAlias('@webroot') . '/comparing.csv';
//$sc = new \common\components\screaming_crawl\ScreamingCrawl();
//$parsed_arr = $sc->parseArray($fB);



//foreach ($parsed_arr['data'] as $row) {
//    echo print_r($sc->wrapRow($row, $parsed_arr['template']));
//}


?>

<style>
    .screaming-crawl-val {
        display: block;
        overflow: hidden;
        height: 1.2em;
        text-overflow: ellipsis;
        word-break: break-all;
    }

    .mb60 {
        margin-bottom: 60px;
    }

    .table td:nth-child(5) {
        width: 200px;
    }
    .table td:nth-child(5) {
        width: 200px;
    }
</style>


<div class="col-xs-12">

    <h1>
        Screaming crawl
        <div class="pull-right">
            <?php $form = \yii\bootstrap\ActiveForm::begin(['method' => 'post']); ?>
            <?= \yii\helpers\Html::submitInput('Export diffs', ['name' => 'export-diffs', 'class' => 'btn btn-default']); ?>
            <?= \yii\helpers\Html::submitInput('Update data', ['name' => 'update-data', 'class' => 'btn btn-primary']); ?>
            <?php \yii\bootstrap\ActiveForm::end() ?>
        </div>
    </h1>

    <div class="row mb60">
        <div class="col-xs-12 ">

            <div class="pull-left">
                <?php $form = \yii\bootstrap\ActiveForm::begin(['method' => 'get']); ?>
                <?= \yii\helpers\Html::submitInput('uncanonical', ['name' => 'option', 'class' => 'btn btn-default']); ?>
                <?php \yii\bootstrap\ActiveForm::end() ?>
            </div>

            <div class="pull-left">
                <?php $form = \yii\bootstrap\ActiveForm::begin(['method' => 'get']); ?>
                <?= \yii\helpers\Html::submitInput('canonical', ['name' => 'option', 'class' => 'btn btn-default']); ?>
                <?php \yii\bootstrap\ActiveForm::end() ?>
            </div>


            <div class="pull-left">
                <?php $form = \yii\bootstrap\ActiveForm::begin(['method' => 'get']); ?>
                <?= \yii\helpers\Html::submitInput('redirects', ['name' => 'option', 'class' => 'btn btn-default']); ?>
                <?php \yii\bootstrap\ActiveForm::end() ?>
            </div>

            <div class="pull-left">
                <?php $form = \yii\bootstrap\ActiveForm::begin(['method' => 'get']); ?>
                <?= \yii\helpers\Html::submitInput('404', ['name' => 'option', 'class' => 'btn btn-default']); ?>
                <?php \yii\bootstrap\ActiveForm::end() ?>
            </div>

        </div>

        <div class="col-xs-12"><br></div>

    </div>

    <?= !empty($window) ? $this->render($window, $window_params) : '' ?>

    <h2>
        Original HTML Grouped by canonical
        <div class="pull-right">
            <?php
            $hideRedirect = isset($_GET['hide-redirect']) ? $_GET['hide-redirect'] : 'false';
            $hideMatches = isset($_GET['hide-matches']) ? $_GET['hide-matches'] : 'false';
            $hideWithoutCouple = isset($_GET['hide-without-couple']) ? $_GET['hide-without-couple'] : 'false';
            ?>
            <?= \yii\bootstrap\Html::a(($hideRedirect == 'false' ? 'hide' : 'show').' redirects', [\yii\helpers\Url::current([
                    'hide-redirect' => ($hideRedirect == 'true' ? 'false' : 'true')
            ])], ['class' => 'btn btn-default']) ?>
            <?= \yii\bootstrap\Html::a(($hideMatches == 'false' ? 'hide' : 'show').' matches', [\yii\helpers\Url::current([
                    'hide-matches' => ($hideMatches == 'true' ? 'false' : 'true')
            ])], ['class' => 'btn btn-default']) ?>
            <?= \yii\bootstrap\Html::a(($hideWithoutCouple == 'false' ? 'hide' : 'show').' without couple', [\yii\helpers\Url::current([
                    'hide-without-couple' => ($hideWithoutCouple == 'true' ? 'false' : 'true')
            ])], ['class' => 'btn btn-default']) ?>
        </div>
    </h2>
    <h3>Total: <?= $dataProvider->totalCount ?></h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <?php foreach ($tds as $td) { ?>
                    <th><?= $td ?></th>
                <?php } ?>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($dataProvider->models as $model) {
                $bModel = \common\components\screaming_crawl\models\CrawlB::findOne(['CanonicalLinkElement1' => $model->CanonicalLinkElement1]);
                $eCount = 0;
                foreach ($tds as $td) {
                    if ($model->$td == $bModel->$td) $eCount++;
                }

                $eCount = $eCount == count($tds);

                $tdClass = '';
                if ($model->sstatus == 1) {
                    $tdClass = 'warning';
                }

                if ($eCount || $model->sstatus == 2) {
                    $tdClass = 'success';
                }

                if ($r = \common\components\screaming_crawl\models\Redirect::findOne(['from' => $model->CanonicalLinkElement1])) {
                    $tdClass = 'info';
                }

                ?>
                <tr class="<?= $tdClass ?>">
                    <?php foreach ($tds as $td) { ?>
                        <td>
                            <span class="screaming-crawl-val"><?= $model->$td ?></span>
                            <span class="screaming-crawl-val">
                                <?php if ($bModel) { ?>
                                    <span style="color: <?= $model->$td === $bModel->$td ? 'blue' : 'red'?>">
                                        <?= $bModel->$td ?>
                                    </span>
                                <?php } else {

                                    if ($r) { ?>
                                        <span style="color: #aaa">[301](<?= $r->to ?>)</span>
                                    <?php } else { ?>
                                        <span style="color: red">Not set</span>
                                    <?php } ?>
                                <?php } ?>
                            </span>
                        </td>
                    <?php } ?>
                    <td width="200px">
                        <?= \yii\bootstrap\Html::a(($model->sstatus == 1 ? 'unsolved' : 'solved'), ['site/screaming-crawl', 'row_id' => $model->id, 'solve' => ($model->sstatus == 1 ? 0 : 1)], [
                            'class' => 'btn btn-default'
                        ]) ?>
                        <?= \yii\bootstrap\Html::a(($model->sstatus == 2 || $eCount ? 'unmatch' : 'match'), ['site/screaming-crawl', 'row_id' => $model->id, 'match' => (($model->sstatus == 2 || $eCount ? 0 : 2))], [
                            'class' => 'btn btn-default'
                        ]) ?>
                    </td>
                </tr>
            <? } ?>
            </tbody>
        </table>
    </div>
    <?= yii\widgets\LinkPager::widget([
        'pagination' => $dataProvider->pagination
    ]) ?>
</div>
