<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

/* @var $productsDataProvider yii\data\ActiveDataProvider */
/* @var $category common\models\LocalCategory */

$products_count = $productsDataProvider->getTotalCount();

Pjax::begin([
    'scrollTo' => 150
]) ?>

<div class="container category-page">

    <div class="category-page__header clearfix">

        <h1 id="category_title"
            class="col-xs-12 col-md-9">
            <?= $category->getH1() ?>
        </h1>

        <span id="totalListCount"
              class="col-xs-12 col-md-3">
            <?= Yii::t('app', '{n} products', [
                'n' => $products_count
            ]) ?>
        </span>

    </div>

    <div class="clearfix" style="position: relative">

        <div class="product_list_title__wrap">
            <div class="product_list_title">

                <?php if ($category && $category->getHeader()) { ?>
                    <div id="category_header" class="hidden-xs">

                        <?php if (\common\models\sale\CyberMonday::isEnabled()) { ?>
                            <img src="/special/cyber-monday/goriz.gif"
                                 style="top: 0; margin-top: 0;">
                        <?php } else { ?>
                            <?= $category->getHeader() ?>
                            <?php if ($category->id == 2) { ?>
                                <div style="position: relative; z-index: 1; color: #fff; padding: 0 20px">
                                    <h2><b>ВЛАДИМИР МОРОЗОВ</b></h2>
                                    <p>
                                        Самый быстрый человек в мире на дистанции 100 м комплексом
                                    </p>
                                </div>
                            <?php } ?>
                        <?php } ?>


                    </div>
                <?php } ?>

                <div class="product_list__sorting">
                    <b><?= Yii::t('app', 'Sort by') ?>:</b>
                    <br class="visible-xs">
                    <?= $category->getSortingSelect($productsDataProvider) ?>
                    <div class="btn-slide_filter"></div>
                </div>

            </div>
        </div>

        <div class="filter_form_wrap">
            <?= $this->render('_filter_form', [
                'category' => $category,
                'stock' => $stock
            ]) ?>
        </div>

        <div class="product_list_container ajax-loading" itemscope itemtype="http://schema.org/CollectionPage">
            <div class="product_list clearfix">
                <?php if ($products_count > 0) {
                    $request = \Yii::$app->request;
                    $n = isset($_GET['page']) ? (($_GET['per-page'] * (Html::encode($request->get('page')) - 1)) + 1) : 1;
                    $list = $category ? $category->title : 'Спецпредложения';

                    foreach ($productsDataProvider->models as $model) {

                        if (($n == 2 && $category->id == 2) || $n == 1 && $category->id == 21 || $n == 1 && $category->id == 19) {
                            echo $this->render('_new_product_streamline', [
                                'v' => \common\models\ProductVariant::findOne(10021309)
                            ]);
                            echo $this->render('_new_product_streamline_rainbow', [
                                'v' => \common\models\ProductVariant::findOne(10021351)
                            ]);
                        }

                        /* @var $model \common\models\ProductVariant */
                        if ($n == 2 && $category->id == 2) {
                            ?>

                            <div class="pinned-preview col-md-6 col-lg-12">
                                <div class="row clearfix">

                                    <div class="col-lg-5 col-lg-push-7">
                                        <div class="video-container">
                                            <iframe width="560" height="315" src="https://www.youtube.com/embed/iQLDVTY_XuQ" frameborder="0" allowfullscreen></iframe>
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-lg-pull-5">
                                        <h3>X-LOOK</h3>
                                        <p>
                                            Инновационная технология расширенного
                                            бокового обзора, идеальная посадка для
                                            максимальной надежности и комфорта, а
                                            также превосходные гидродинамические
                                            характеристики объединены в одной из
                                            лучших моделей стартовых очков
                                            Mad Wave - X-LOOK
                                        </p>
                                        <br>
                                        <p>
                                            <a href="https://www.madwave.ru/catalog/ochki-dlya-plavaniya/startovye-ochki/x-look?sku=10018964" class="btn btn-primary">Подробнее</a>
                                        </p>
                                    </div>

                                </div>
                            </div>
                            <?php
                        }
                        echo $model->preview($list, $n, null);
                        $n++;
                    }
                } else {
                    echo $this->render('_empty_state');
                } ?>
            </div>
            <div id="product_list_pagination" class="text-center">
                <?= LinkPager::widget(['pagination' => $productsDataProvider->pagination]);  ?>
            </div>
        </div>

    </div>

    <hr class="category-description-divider">

    <section id="category-description">
        <p><?= $category->getDesc() ?></p>
    </section>

</div>

<?php Pjax::end() ?>