<!DOCTYPE html>
<html>
<head>
    <!-- MADE WITH LOVE BY <> SERGEY KHMELEVSKOY </>-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Соревнования по плаванию Mad Wave Challenge</title>
    <meta name="description" content="Турнир и соревнования по плаванию Mad Wave Challenge - официальная страница для регистрации участников">
    <link  rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="/bundles/challenge/css/style.min.css">
    <link rel="stylesheet" href="/bundles/flipclock/flipclock.css">
</head>
<body>
<!-- HEADER-->
<header class="header">
    <div class="container">
        <div class="row hidden-md-down">
            <div class="col-sm-3"><a href="#"><i class="ico ico-logo"></i></a></div>
            <div class="col-sm-7">
                <ul class="header__navi">
                    <li><a href="https://www.madwave.ru/">Онлайн-Магазин</a></li>
                    <li><a href="http://swim4you.ru/">Сайт соревнований</a></li>
                    <li><a href="https://mwchallenge.ru/">Стать участником</a></li>
                </ul>
            </div>
            <div class="col-sm-2 text-sm-right">
                <div class="header__socials">
                    <a href="mailto:info@swim4you.ru"><i class="ico ico-mail"></i></a>
                    <a href="https://www.instagram.com/swim4you.ru/"><i class="ico ico-insta"></i></a>
                    <a href="https://vk.com/mwchallenge"><i class="ico ico-vk"></i></a></div>
            </div>
        </div>
        <div class="row hidden-lg-up">
            <div class="col-xs-6"><a href="#"><i class="ico ico-logo"></i></a></div>
            <div class="col-xs-3 text-xs-center">
                <div class="header__socials">
                    <a href="mailto:info@swim4you.ru"><i class="ico ico-mail"></i></a>
                    <a href="https://www.instagram.com/swim4you.ru/"><i class="ico ico-insta"></i></a>
                    <a href="https://vk.com/mwchallenge"><i class="ico ico-vk"></i></a></div>
            </div>
            <div class="col-xs-3 text-xs-right">
                <button class="hamburger hamburger--elastic" type="button"><span class="hamburger-box"><span class="hamburger-inner"></span></span></button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="mobile-nav">
                    <ul class="mobile-nav-list">
                        <li><a href="https://www.madwave.ru/">Онлайн-Магазин</a></li>
                        <li><a href="http://swim4you.ru/">Сайт соревнований</a></li>
                        <li><a href="https://mwchallenge.ru/">Стать участником</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
<!--HERO-->
<section class="hero">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 text-xs-center"><i class="ico ico-challange"></i>
                <div class="hero__title">Mad Wave Challenge</div>
                <div class="hero__desc">Международные всероссийские соревнования <br> и Турнир по плаванию для юниоров от 10 до 14 лет</div><a class="btn-cta" href="https://mwchallenge.ru/">Стать участником</a>
            </div>
        </div>
    </div>
</section>
<!-- EVENTS-->
<section class="events">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="heading">Турнир 2017-2018</div>
                <div class="subheading">Этапы</div>
            </div>
        </div>
        <div class="row">
            <?php

            use Vinkla\Instagram\Instagram;

            $target = null;
            foreach ($data as $i => $d) {
                /** @var $d \common\models\content\ChallengeSteps */
                $time = time();
                $now = date('d.m.Y');
                $date_end = strtotime($d->date_to);
                $isComplete = $time > $date_end;
                $date = explode('.', $d->date_from);
                $exploded_date = ['d' => $date[0], 'm' => $date[1], 'y' => $date[2]];
                if (!$isComplete && $target == null) {
                    $target = $exploded_date;
                }
                ?>
                <div class="col-sm-3">
                    <div class="events__item <?= $isComplete ? 'events__item--compleated' : '' ?>">
                        <i class="ico events__item__status"></i>
                        <div class="events__item__image">
                            <img src="<?= $d->icon ?>" alt="<?= $d->title ?>">
                        </div>
                        <div class="events__item__name"><?= $d->title ?></div>
                        <div class="events__item__date"><?= $d->getDateRangeString() ?></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
<!--section-->
<?php if ($target) {?>
<section class="counter">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="heading heading--small">До следующего этапа осталось:</div>
                <style>
                    .flip-clock-divider, .flip, .flip-clock-wrapper {
                        display: inline-block;
                        float: none !important;
                    }
                    @media (max-width: 710px) {
                        .counter {
                            display: none;
                        }
                    }
                </style>
                <br>
                <br>
                    <div class="your-clock text-center"></div>
            </div>
        </div>
    </div>
</section>
<?php } ?>
<!-- About-->
<section class="about">
    <div class="container">
        <div class="row">
            <div class="heading">О соревнованиях</div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-8">
                <p>
                    «Mad Wave Challenge» — это крупнейшие детские соревнования для спортсменов от 9 до 14 лет на территории России и Белоруссии.  За три года проведения турнира в соревнованиях приняли участие более 5000 юных пловцов из таких стран, как Литва, Польша, Молдавия, Украина, Казахстан, Норвегия, Финляндия, Эстония, Филиппины и, конечно, Россия и Беларусь.
                </p>
                <p>
                    Соревнования проходят в четыре этапа и объединены в общий Турнир. Первым старт вновь примет Брест (4-5 ноября). С этапа в Санкт-Петербурге (20-21 января) начнется российская серия стартов, которая продолжится в Рузе (17-18 марта) и завершится в Казани (4-6 мая). Именно в столице Татарстана определятся победители личного и командного первенств Турнира сезона 2017/18.
                </p>
                <p>
                    Соревнования проходят в четыре этапа и объединены в общий Турнир. Первым старт вновь примет Брест (4-5 ноября). С этапа в Санкт-Петербурге (20-21 января) начнется российская серия стартов, которая продолжится в Рузе (17-18 марта) и завершится в Казани (4-6 мая). Именно в столице Татарстана определятся победители личного и командного первенств Турнира сезона 2017/18.
                </p>
            </div>
            <div class="about__image" style="bottom: 0 !important; top: inherit"></div>
        </div>
    </div>
</section>


        <?php try {

            $instagram = new Instagram('1649721240.1677ed0.0485b1f78687487ab49b2d378012df9c');
            $result = $instagram->get();

            if ($result) { ?>
            <!-- gallery-->
            <section class="gallery">
                <div class="container popup-gallery">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="heading heading--white">Галерея</div>
                    </div>
                </div>
                <?php foreach ($result as $i => $p) {
                    if ($i == 0) {
                        echo '<div class="row">';
                    }
                    ?>
                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <a class="gallery__image" href="<?= $p->images->standard_resolution->url ?>">
                            <img class="img-fluid" src="<?= $p->images->low_resolution->url ?>" alt="Mad Wave Challenge"></a>
                    </div>
                    <?php
                    if ($i == 7) {
                        echo '</div><div class="row gallery__more">';
                    }
                    if ($i == 19) {
                        echo '</div>';
                    }
                } ?>
                <div class="row">
                    <div class="col-sm-12" style="text-align: center"><span class="btn-outline" id="morePhotos">Больше фотографий</span></div>
                </div>

                </div>
            </section>
            <?php
            }
        } catch (Exception $e) {
        };


        ?>
<!--partners-->
<section class="partners">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="heading">Наши партнеры</div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 owl-carousel" id="owlPartners">
                <?php
                $partners = [
                    [
                        'name' => 'ПТК Спорт',
                        'img' => 'ptk.jpg',
                        'description' => 'Производственно-торговая компания "Спорт" является крупнейшим в России производителем широкого спектра оборудования для оснащения бассейнов и водных видов спорта. История компании началась в 2001 году.'
                    ],
                    [
                        'name' => 'ВФП',
                        'img' => 'vpf.png',
                        'description' => 'Всероссийская федерация плавания (ВФП) – общественная организация, ставящая целью развитие, пропаганду и популяризацию плавания в Российской Федерации. Президент - Сальников Владимир Валерьевич.'
                    ],
                    [
                        'name' => 'Академия Спорта',
                        'img' => 'logotip_akademiya_sporta.png',
                        'description' => 'Академия Спорта - ведущий российский оператор по проведению соревнований, спортивных лагерей, мастер-классов и методическому обеспечению подготовки спортсменов.'
                    ],
                ];
                foreach ($partners as $prtn) { ?>

                <div class="partners__block">
                    <div class="partners__image"><img class="img-fluid" src="/bundles/challenge/images/<?= $prtn['img'] ?>" alt="Mad Wave Challenge Partner"></div>
                    <div class="partners__name"><?= $prtn['name'] ?></div>
                    <div class="partners__content"><?= $prtn['description'] ?></div>
                </div>

                <?php } ?>
            </div>
        </div>
    </div>
</section>
<!--products-->
<section class="products">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="heading">Будь первым с Mad Wave!</div>
                <div class="subheading subheading--light">Добивайся лучших результатов с экипировкой для профессионального плавания Mad Wave. <br> Стартовые очки, гидрокостюмы, джаммеры и шапочки:</div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 owl-carousel" id="owlProducts">
                <?php foreach ($products as $v) {
                    $product = $v->product;
                    ?>
                    <div class="product">
                        <div class="product__image">
                            <a href="<?= $v->url ?>">
                                <img class="img-responsive"
                                     src="<?= $v->getImage(1, $v::IMG_MEDIUM, true, true) ?>"
                                     alt="<?= $p->name ?>">
                            </a>
                        </div>
                        <div class="product__name"><?= $product->name ?></div>
                        <div class="product__desc"><?= $product->globalCategory->name_ru ?></div>
                        <div class="product__price">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="product__old">
                                        <?php if ($v->salePrice) { ?>
                                            <span style="text-decoration: line-through"><?= $v->getOldPrice() ?> Р</span>
                                        <?php } elseif ($product->discount_card !== 1) { ?>
                                            <?= $v->price ?> P
                                        <?php } else { ?>
                                            &nbsp;
                                        <?php } ?>
                                    </div>
                                    <div class="product__new">
                                        <?php if ($v->salePrice) { ?>
                                            <small class="fsize-1 price"><?= $v->getPrice() ?> Р</small>
                                        <?php } elseif ($product->discount_card == 1) { ?>
                                            <small class="fsize-1 price"><?= $v->getPrice() ?> Р</small>
                                        <?php } else { ?>
                                            <small class="fsize-1 price color-primary"><?= $v->getCardPrice() ?> Р</small>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-sm-6"><a class="btn-buy" href="<?= $v->url ?>"><i class="ico ico-buy"></i></a></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
<!--products-->
<section class="products products--second">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="heading">Сувенирная продукция</div>
                <div class="subheading subheading--light">Продли воспоминания о любимых соревнованиях <br>с линейкой сувенирной продукции Mad Wave Challenge!</div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 owl-carousel" id="owlSouvenier">
                <?php
                foreach ($souvenirs as $v) {
                    $product = $v->product;
                    ?>
                <div class="product">
                    <div class="product__image">
                        <a href="<?= $v->url ?>">
                        <img class="img-responsive" src="<?= $v->getImage(1, $v::IMG_MEDIUM, true, true) ?>" alt="<?= $p->name ?>">
                        </a>
                    </div>
                    <div class="product__name"><?= $product->name ?></div>
                    <div class="product__desc"><?= $product->globalCategory->name_ru ?></div>
<!--                    <div class="product__stars"><i class="ico ico-star"></i><i class="ico ico-star"></i><i class="ico ico-star"></i><i class="ico ico-star"></i><i class="ico ico-star ico-star--blank"></i><span>(4.8 ср. рейтинг)</span></div>-->
                    <div class="product__price">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="product__old">
                                    <?php if ($v->salePrice) { ?>
                                        <span style="text-decoration: line-through"><?= $v->getOldPrice() ?> Р</span>
                                    <?php } elseif ($product->discount_card !== 1) { ?>
                                        <?= $v->price ?> P
                                    <?php } else { ?>
                                        &nbsp;
                                    <?php } ?>
                                </div>
                                <div class="product__new">
                                    <?php if ($v->salePrice) { ?>
                                        <small class="fsize-1 price"><?= $v->getPrice() ?> Р</small>
                                    <?php } elseif ($product->discount_card == 1) { ?>
                                        <small class="fsize-1 price"><?= $v->getPrice() ?> Р</small>
                                    <?php } else { ?>
                                        <small class="fsize-1 price color-primary"><?= $v->getCardPrice() ?> Р</small>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-sm-6"><a class="btn-buy" href="<?= $v->url ?>"><i class="ico ico-buy"></i></a></div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
<!--HERO-->
<section class="hero hero--bottom">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 text-xs-center">
                <div class="hero__uptitle">Стань участником</div>
                <div class="hero__title">Mad Wave Challenge</div>
                <div class="hero__desc">Окунись в мир профессионального плавания <br> вместе с Mad Wave</div><a class="btn-cta" href="https://mwchallenge.ru/">Стать участником</a>
            </div>
        </div>
    </div>
</section>
<!--footer-->
<footer class="footer text-xs-center text-sm-left">
    <div class="container">
        <div class="row">
            <div class="col-sm-3"><a href="#"><i class="ico ico-logo"></i></a></div>
            <div class="col-sm-7">
                <div class="footer__copy">Все права зщищены Адрес: Санкт-Петербург, Индустриальный Проспект, 70</div>
            </div>
            <div class="col-sm-2 text-sm-right">
                <div class="header__socials">
                    <a href="mailto:info@swim4you.ru"><i class="ico ico-mail"></i></a>
                    <a href="https://www.instagram.com/swim4you.ru/"><i class="ico ico-insta"></i></a>
                    <a href="https://vk.com/mwchallenge"><i class="ico ico-vk"></i></a></div>
                </div>
            </div>
        </div>
    </div>
</footer>
<script src="/bundles/challenge/js/main.min.js"></script>
<?php if ($target) {

    $date = mktime(12, 0, 0, $target['m'], $target['d'], $target['y']) - time();
    ?>

    <script src="/bundles/flipclock/flipclock.min.js"></script>
    <script>
        var clock = $('.your-clock').FlipClock(<?= $date > 0 ? $date : 0 ?>, {
            clockFace: 'DailyCounter',
            countdown: true,
            language: 'ru'
        });
    </script>

<?php } ?>

</body>
</html>
