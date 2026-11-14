<?php
use common\components\screaming_crawl\models\CrawlDiffs;
use yii\widgets\Menu;

$diff = CrawlDiffs::find()->where(['CanonicalLinkElement1' => \common\components\screaming_crawl\ScreamingCrawl::cutUrl($model->getUrl())])->one();


if ($diff && !empty($diff->Title1)) {
    $this->title = $diff->Title1;
} else {
    $this->title = $model->title .' | Mad Wave';
}

if ($diff && !empty($diff->MetaDescription1)) {
    $this->registerMetaTag(['name'=>'description', 'content' => $diff->MetaDescription1]);
} elseif (!empty($model->meta_description)) {
    $this->registerMetaTag(['name'=>'description', 'content' => $model->meta_description]);
}



?>

<!--<script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>-->
<!--<script src="//yastatic.net/share2/share.js"></script>-->
<!---->
<!--<style>-->
<!--    h1 {-->
<!--        margin-bottom: 30px !important;-->
<!--    }-->
<!--</style>-->
<!--<div class="col-xs-12"></div>-->
<!--<div class="container">-->
<!--    <div class="col-md-3">-->
<!--    <div class="col-md-6">-->
<!--        <h3 class="h4" style="margin-bottom: 30px">-->
<!--            --><?//= date('d-m-Y', $model->created_at) ?>
<!--            <a href="--><?//= $model->category->url ?><!--" class="label label-info">--><?//= $model->category->title ?><!--</a>-->
<!--            <div class="pull-right">-->
<!--                <div class="ya-share2" data-services="vkontakte,facebook,gplus,twitter" data-counter=""></div>-->
<!--            </div>-->
<!--        </h3>-->
<!--        <h1>--><?//= $model->title ?><!--</h1>-->

<!--    </div>-->
<!--</div>-->

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
    <script src="https://yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
    <script src="https://yastatic.net/share2/share.js"></script>
    <div class="article-hero" style="background-image: url('<?= $model->getCoverUrl(); ?>')">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="article-hero-crumbs">
                        <a href="<?= \Yii::$app->getHomeUrl() ?>">Главная</a><span class="crumbs-separator">></span>
                        <a href="<?= \yii\helpers\Url::to('/world/') ?>">Блог</a><span class="crumbs-separator">></span>
                        <a class="active" href="#"><?= $model->title ?></a>
                    </div>
                    <div class="article-metrics">
                        <div class="article-watch"><i class="ico ico-article-watch"></i><span><?= $model->views ?></span></div>
<!--                        <div class="article-likes"><i class="ico ico-article-likes"></i><span>231</span></div>-->
                    </div>
                    <?php
                    if ($diff && !empty($diff->H11)) {
                        $h1 = $diff->H11;
                    } else {
                        $h1 = $model->title;
                    }
                    ?>
                    <h1 class="article-heading"><?= $h1 ?></h1>
                    <div class="article-share">
                        <div
                            class="ya-share2"
                            data-services="vkontakte,facebook,gplus,twitter"
                            data-counter=""
                            data-url="https://www.madwave.ru/world/<?= $model->slug ?>"
                            data-title="<?= $h1 ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container blog-page blog-page--inner">
        <div class="row">
            <div class="col-md-8 article-content">

                <?= $model->content ?>

                <div class="article-content-share">
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="article-content-share-title">Поделиться с друзьями</div>
                            <div class="article-share m0">
                                <div
                                        class="ya-share2"
                                        data-services="vkontakte,facebook,gplus,twitter"
                                        data-counter=""
                                        data-url="https://www.madwave.ru/world/<?= $model->slug ?>"
                                        data-title="<?= $h1 ?>">
                                </div>
                            </div>
                        </div>
<!--                        <div class="col-sm-4 text-right hidden-xs"><a class="btn btn-outline mt30" href="#sectionComment">2 Комментария</a></div>-->
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <?= $this->render('_global_aside', [
                    'pinnedPost' => $pinnedPost,
                    'categories' => $categories
                ]) ?>
            </div>
        </div>

<!-- NEW CONTENT::COMMENTS SECTION-->
<!--        <div class="row">-->
<!--            <div class="col-sm-8">-->
<!--                <h2>Комментарии</h2>-->
<!--                <div class="comment" data-id="1">-->
<!--                    <div class="row">-->
<!--                        <div class="col-xs-2 col-sm-2">-->
<!--                            <div class="comment-img"><img class="img-responsive" src="http://localhost:3000/images/comment-ava.png"></div>-->
<!--                        </div>-->
<!--                        <div class="col-xs-10 col-sm-10">-->
<!--                            <div class="comment-author">Алексей Воеводин</div>-->
<!--                            <div class="comment-content">Наряду с этим, агентская комиссия концентрирует поведенческий таргетинг. Портрет потребителя искажает сублимированный анализ зарубежного опыта, осознав маркетинг как часть производства. Опрос спонтанно допускает поведенческий таргетинг.</div>-->
<!--                            <div class="comment-timestamp">Вчера в 21:00</div>-->
<!--                            <div class="comment-actions"><a href="#">Ответить</a></div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="row">-->
<!--            <div class="col-sm-6 col-lg-4" id="sectionComment">-->
<!--                <form class="comment-form" action="">-->
<!--                    <div class="form-group form-group--bold-label">-->
<!--                        <label>Укажите e-mail, чтобы оставить комментарий</label>-->
<!--                        <input class="form-control rad0" placeholder="Ваш email">-->
<!--                    </div>-->
<!--                    <div class="form-group">-->
<!--                        <textarea class="form-control rad0" placeholder="Ваш комментарий"></textarea>-->
<!--                    </div>-->
<!--                    <div class="form-group">-->
<!--                        <button class="btn btn-lg btn-primary" type="submit">Отправить</button>-->
<!--                    </div>-->
<!--                </form>-->
<!--            </div>-->
<!--        </div>-->
    </div>
