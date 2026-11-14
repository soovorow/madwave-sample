<?php
$this->title = '2/3 МИРА - ПОБЕДНЫЕ НАБОРЫ СИЛЬНЕЙШИХ ПЛОВЦОВ МИРА ОТ MAD WAVE';

$this->registerJs(<<<JS
    $('body').on('click', '[data-loadview]', function () {
        var container =  $(this).parents('#variant');
        $.ajax({
            'type': 'post',
            'url': '/ajax/load-new-view',
            'data': {'sku': $(this).data('loadview')},
            'beforeSend': function () {container.addClass('loading')},
            'complete': function () {container.removeClass('loading')},
            'success': function (response) {
                var r = JSON.parse(response);
                container.html($(r.view));
                window.history.pushState(null,'hi','?sku='+r.sku);
                zoom();
                ga('send', 'pageview', window.location.href );
            }
        })
    });
    $('body').ready(function() {
        $('#slick-2of3').slick({
            arrows: false,
            infinite: false,
            slidesToShow: 2,
            responsive: [
                {
                    breakpoint: 970,
                    settings: {
                        slidesToShow: 1,
                        arrows: true
                    }
                }
            ]
        });
        
        var mFrame = $('#morozov-frame');
        var pFrame = $('#prigoda-frame');
        
        var mProducts = $('#morozov-2of3-products');
        var pProducts = $('#prigoda-2of3-products');
        
        mProducts.slick({
            slidesToShow: 1,
            infinite: false,
			adaptiveHeight: true
        });
        
        mFrame.click(function() {
            $(this).addClass('active');
            pFrame.removeClass('active');
            
            pProducts.removeClass('active');
            mProducts.addClass('active');
            
            pProducts.slick("unslick");
            mProducts.slick({
                slidesToShow: 1,
                infinite: false,
				adaptiveHeight: true
            });
        });
        
        pFrame.click(function() {
            $(this).addClass('active');
            mFrame.removeClass('active');
            
            mProducts.removeClass('active');
            pProducts.addClass('active');
            
            mProducts.slick('unslick');
            pProducts.slick({
                slidesToShow: 1,
                infinite: false,
				adaptiveHeight: true
            });
        });
        
        $('#choose-btn').on('click', function() {
            $('html, body').animate({
                scrollTop: $('#slick-2of3').offset().top - 100
            }, 1000, 'swing');
        });
        
    })

JS
);
?>

<section id="page-2of3">
    <div class="promo">
        <div class="content">
            <div class="info">
                <div class="row">
                    <div class="col-md-4">
                        <div class="left">
                            <div class="name">Владимир Морозов</div>
                            <div class="desc">Чемпион Кубка мира 2016, один из сильнейших пловцов на Кубке мира FINA/airweave 2017, бронзовый призер Олимпийских игр 2012 года</div>
                        </div>
                    </div>
                    <div class="col-md-4" style="padding: 0;">
<!--                        <div class="ref">* По результатам Кубка мира FINA / airweave  по плаванию 2017</div>-->
                        <div id="choose-btn" class="fake-btn">Выбрать победный набор</div>
                        <hr>
                    </div>
                    <div class="col-md-4">
                        <div class="right">
                            <div class="name">Кирилл Пригода</div>
                            <div class="desc">Рекордсмен мира и Европы, чемпион и рекордсмен России, один из сильнейших пловцов на Кубке мира FINA/airweave 2017</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sportsmen">
        <div class="container">
            <div id="slick-2of3">
                <div id="morozov-frame" class="frame active">
                    <img src="/images/2_3/morozov.jpg">
                    <div class="info">
                        <div class="name">Владимир Морозов</div>
                        <div class="desc">«Правильный выбор экипировки — залог успешных стартов и отличных результатов.  Для себя я выбрал гидрокостюмы Air Force и FORCESHELL 2017, в них я уверен в том, что максимально эффективно отработаю свою дистанцию. Уровень компрессии мышц в них оптимален, и скорость в воде действительно выше»</div>
                    </div>
                    <div class="indicator"><i class="fa fa-check"></i></div>
                </div>
                <div id="prigoda-frame" class="frame">
                    <img src="/images/2_3/prigoda.jpg">
                    <div class="info">
                        <div class="name">Кирилл Пригода</div>
                        <div class="desc">«Для победы важна каждая мелочь! Гидрокостюм FORCESHELL 2017 –  то, что нужно для успешных стартов. Он обеспечивает максимальную компрессию мышц, а также идеальное скольжение  в воде, позволяя мне двигаться быстрее» </div>
                    </div>
                    <div class="indicator"><i class="fa fa-check"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="products">
                <div id="morozov-2of3-products" class="sportsman-products active">
                    <?php
                    /**
                     * @var $morozov \common\models\ProductVariant[]
                     */
                    foreach ($morozov as $v): ?>
                        <div class="product">
                            <div id="variant" class="ajax-loading">
                                <?= $this->render('../catalog/_view_new', [
                                    'v' => $v,
                                    'model' => $v->product,
                                    'show_product_page_button' => true
                                ]); ?>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
                <div id="prigoda-2of3-products" class="sportsman-products">
                    <?php
                    /**
                     * @var $prigoda \common\models\ProductVariant[]
                     */
                    foreach ($prigoda as $v): ?>
                        <div class="product">
                            <div id="variant" class="ajax-loading">
                                <?= $this->render('../catalog/_view_new', [
                                    'v' => $v,
                                    'model' => $v->product,
                                    'show_product_page_button' => true
                                ]); ?>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>
</section>
