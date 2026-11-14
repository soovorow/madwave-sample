<?php

/* @var $city string */
/* @var $stores \yii\db\Query */
/* @var $stores_result_view string */
/* @var $stores_all_view string */

$this->title = 'Фирменные магазины Mad Wave | Mad Wave';

$this->registerMetaTag(['name' => 'description', 'content' => 'Фирменные магазины Mad Wave (Доступны только в некоторых городах) - Доставка Бесплатная Бесплатная доставка Вашего заказа в любой магазин фирменной розничной сети Mad Wave!Срок доставки: от 2 до 15 рабочих дней']);

$this->params['breadcrumbs'][] = 'Магазины';

?>

<div class="container">
    <div class="product-view row mb60" style="padding-top: 0">
        <div class="ajax-loading" id="variant">
            <div class="clearfix">
                <div class="col-xs-12 top-caption">
                    <h1 class="hidden">Фирменные магазины Mad Wave</h1>
                    <h2 style="margin-top: 15px">
                        <?php if ($stores->count() > 0): ?>
                            Адреса магазинов в городе
                        <?php else: ?>
                            К сожалению, в городе
                        <?php endif; ?>

                        <span class="a call-city-picker pointer" data-reload="true" style="text-transform: capitalize">
                            <?= $city ?>
                        </span>

                        <?php if ($stores->count() > 0): ?>
                        <?php else: ?>
                            нет магазинов,
                            <br>
                            <small>Но по всей России доступно более 20</small>
                        <?php endif; ?>
                    </h2>
                </div>
            </div>
            <div class="col-xs-12">
                <?php if ($stores->count() > 0): ?>
                    <div class="shops-wrapper">
                        <!-- top filter-->
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group form-group--bold-label">
                                    <label>Введите адрес магазина</label>
                                    <input id="search-store" class="form-control rad0 form-control--search" type="text" placeholder="Например: Индустриальный проспект 70">
                                    <!--                                    <div class="shops-quick-links"><a href="#">Электроный каталог</a><a href="#">Погашение кредита</a><a href="#">Терминал оплаты</a><a href="#">Настройки</a><a href="#">Банкомат</a><a href="#">Выдача кредитных карт</a></div>-->
                                </div>
                            </div>
                            <!--                            <div class="col-xs-12">-->
                            <!--                                <div class="row">-->
                            <!--                                    <div class="col-sm-6">-->
                            <!--                                        <div class="form-group form-group--bold-label">-->
                            <!--                                            <label>Режим работы</label>-->
                            <!--                                            <input class="form-control form-control--short rad0" type="text" placeholder="с">-->
                            <!--                                            <input class="form-control form-control--short rad0" type="text" placeholder="по">-->
                            <!--                                        </div>-->
                            <!--                                    </div>-->
                            <!--                                    <div class="col-sm-6 mt20 shops-checkboxes">-->
                            <!--                                        <label class="clearfix">-->
                            <!--                                            <input class="checkbox" name="shops24h" type="checkbox"><span></span>24 часа-->
                            <!--                                        </label>-->
                            <!--                                        <label class="clearfix">-->
                            <!--                                            <input class="checkbox" name="shopsOpenNow" type="checkbox"><span></span>Работает сейчас-->
                            <!--                                        </label>-->
                            <!--                                    </div>-->
                            <!--                                </div>-->
                            <!--                            </div>-->
                        </div>
                        <!-- sorting-->
                        <div class="row hidden">
                            <div class="col-sm-6">
                                <div class="shops-result-num">
                                    Найдено
                                    <?= $stores->count() ?>
                                    магазинов
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="pull-right"><a class="shops-filter-btn" href="#"><i class="ico ico-filter"></i><span>Списком</span></a><a class="shops-filter-btn active" href="#"><i class="ico ico-loc"></i><span>На карте</span></a></div>
                            </div>
                        </div>
                    </div>
                    <!-- mobile sorting-->
                    <div class="shops-wrapper-mobile visible-xs">
                        <div class="row">
                            <!--                            <div class="col-xs-3"><a class="btn btn-outline" id="shopsShowFilters" href="#"><i class="fa fa-filter"></i></a></div>-->
                            <div class="col-xs-6 text-left"><a class="btn btn-outline active" id="shopsShowList" href="#"><i class="fa fa-bars"></i></a><a class="btn btn-outline" id="shopsShowMap" href="#"><i class="fa fa-map-marker"></i></a></div>
                            <!--                            <div class="col-xs-6 text-right"><a class="btn btn-outline" href="#"><i class="fa fa-search"></i></a></div>-->
                        </div>
                    </div>

                    <?= $stores_result_view ?>
                <?php else: ?>
                    <?= $stores_all_view ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <hr>
    <p class="category-description text-center">
        Фирменные магазины Mad Wave (доступны не во всех городах) <br>
        Бесплатная доставка Вашего заказа в любой магазин фирменной розничной сети Mad Wave! <br>
        Срок доставки: от 2 до 15 рабочих дней
    </p>
</div>
