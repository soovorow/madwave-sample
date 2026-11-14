<?php
use common\components\screaming_crawl\models\CrawlDiffs;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Menu;

$diff = CrawlDiffs::find()->where(['CanonicalLinkElement1' => \common\components\screaming_crawl\ScreamingCrawl::cutUrl(\yii\helpers\Url::canonical())])->one();

if ($diff && !empty($diff->Title1)) {
    $this->title = $diff->Title1;
} else {
    $this->title = !empty($category->title) ? $category->title : 'WORLD' . ' | Mad Wave';
}

if ($diff && !empty($diff->MetaKeyword1)) {
    $this->registerMetaTag(['name' => 'keywords', 'content' => $diff->MetaKeyword1]);
}

if ($diff && !empty($diff->MetaDescription1)) {
    $this->registerMetaTag(['name' => 'description', 'content' => $diff->MetaDescription1]);
}

/** @var $dataProvider \yii\data\ActiveDataProvider */
?>
<style>
    .list-group-item.active a {
        color: #fff;
    }

    .attachment > iframe {
        width: 100%;
    }

    hr {
        margin: 50px 0;
    }
</style>

<script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
<script src="//yastatic.net/share2/share.js"></script>
<div class="container blog-page">
    <h2>Новостная лента</h2>
    <div class="row">
        <div class="col-md-8 post-flow">
            <div class="row">
                <?php echo ListView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => "{items}\n{pager}",
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => function ($model) {
                        return $this->render('_preview', ['post' => $model]);
                    },
                    'pager' => [
                        'class' => \kop\y2sp\ScrollPager::className(),
                        'triggerTemplate' => '<div class="col-xs-12"><span class="pager-trigger pager-trigger--primary"><i class="fa fa-refresh"></i> Загрузить еще</span></div>',
                        'spinnerTemplate' => '<div class="col-xs-12"><span class="pager-trigger pager-trigger--primary"><i class="fa fa-refresh fa-spin"></i> Загрузить еще</span></div>',
                        'noneLeftText' => '<div class="col-xs-12"><span class="goHome pager-trigger ">В начало</span></div>'
                    ]
                ]); ?>
            </div>
        </div>
        <div class="col-md-4">

            <?= $this->render('_global_aside', [
                'pinnedPost' => $pinnedPost,
                'categories' => $categories,
            ]) ?>

        </div>
    </div>
</div>