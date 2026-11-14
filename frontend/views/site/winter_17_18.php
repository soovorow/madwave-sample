<?php

use common\models\content\PinnedItems;
use common\models\ProductVariant;
use frontend\models\ProductSearch;
use frontend\modules\citydetector\CityDetector;

$w = new \common\models\sale\Winter17_18();
//$w->enableDiscounts();
//$w->productRotation();
$this->title = 'Новогодний MEGA SALE! Сотни товаров со скидками до 60%! Обновление ЕЖЕДНЕВНО!';
$this->registerMetaTag(['name'=>'description', 'content' => 'Успейте подписаться на самые горячие скидки в нашем интернет-магазине! Начать покупки!']);

?>


    <section style="background: #000; margin-bottom: 60px">
        <div class="container">
            <div class="banner"
                 style="background: url(/Sale_60.gif) center center / cover no-repeat;
                 height: 300px;
            ">

            </div>
        </div>
    </section>

<?php if (in_array(CityDetector::getCity(), ['Москва', 'Санкт-Петербург']) && time() + (3 * 60 * 60) < mktime(0,0,0,12,28,2017) + (3 * 60 * 60)) { ?>
    <section style="margin-bottom: 40px">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-push-2">
                    <div class="text-center" style="border: 1px solid #f2f2f2; padding: 30px">
                        <div style="
                            width: 60px;
                            height: 60px;
                            margin: 0 auto;
                            border: 5px solid #f08750;
                            border-radius: 100%;
                            padding: 13px 10px;
                        ">
                            <img src="/images/icons/delivery.png" alt="delivery" style="width: 100%">
                        </div>
                        <h2 style="font-size: 26px">
                            Порадуйте себя и близких!
                            При оформлении заказа до полуночи 27 декабря
                            с доставкой курьерской службой
                            мы успеем доставить его до Нового Года!
                        </h2>
                        <p style="font-size: 16px; color: #606060">
                            Условие допустимо только в городах Санкт-Петербург и Москва,
                            при заказе товара с доставкой "Доставка курьером по СПб", "Доставка курьером по Москве"
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<section id="lighting">
    <style>
        #lighting {
            padding-top: 30px;
        }
        #lighting h2 {
            font-size: 67px;
            text-transform: uppercase;
        }

        @media (max-width: 798px) {
            #lighting h2 {
                font-size: 57px;
            }
        }

        #lighting p {
            font-size: 16px;
        }

        #lighting .timer {
            text-transform: uppercase;
            font-weight: bold;
        }
        #lighting-counter {
            font-size: 42px;
            line-height: 42px;
            margin-top: 5px;
        }
        #lighting span.timer-caption {
            font-size: 10px;
        }

        #lighting span.timer-caption:not(:last-child) {
            margin-right: 25px;
        }

        #lighting hr {
            width: 50%;
            margin-left: -30px
        }

        #lighting .product {
            border: none;
            position: relative;
        }

        #lighting .product .background {
            content: '';
            display: block;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 60px;
            right: 0;
            background: #F5F5F5;
            border: none;
            z-index: -2;
        }

        @media (max-width: 798px) {
            #lighting .product .background {
                display: none;
            }
        }

        #lighting .product h2 {
            font-size: 48px;
            color: #000000;
            letter-spacing: 0;
            line-height: 48px;
            margin-bottom: 15px;
        }


        #lighting .product p {
            opacity: 0.7;
            font-size: 16px;
            color: #282828;
            letter-spacing: 0;
            line-height: 22px;
            margin-bottom: 40px;
        }

        #lighting .product .percent {
            width: 66px;
            height: 66px;
            transform: rotate(-45deg);
            background: #FF8141;
            position: absolute;
            right: -37px;
            top: 15px;
            z-index: -1;
        }

        @media (max-width: 798px) {
            #lighting .product .percent {
                right: 0px;
            }
        }

        #lighting .product .percent span {
            font-size: 25px;
            color: #FFFFFF;
            letter-spacing: 0;
            transform: rotate(45deg);
            display: block;
            text-align: center;
            line-height: 66px;
        }

        #lighting .product .price {
            font-size: 35px;
            color: #000000;
            letter-spacing: 0;
            line-height: 40px;
            margin-right: 23px;
            display: inline-block;
            margin-bottom: 28px;
        }

        #lighting .product .old-price {
            opacity: 0.3;
            font-size: 34px;
            color: #333333;
            letter-spacing: 0;
            line-height: 40px;
            text-decoration: line-through;
            display: inline-block;
            margin-bottom: 28px;
        }

        #lighting .product .link {
            margin-bottom: 30px;
            background: #000000;
            box-shadow: 10px 20px 20px 0 rgba(0,0,0,0.08);
            font-size: 14px;
            color: #FFFFFF;
            letter-spacing: 0.3px;
            line-height: 19px;
        }

    </style>
    <?php

    $time = date('Y/m/d', strtotime('+1 day'));

    $this->registerJsFile('/js/jquery.countdown.min.js', ['depends' => ['yii\web\JqueryAsset']]);
    $this->registerJs(<<<JS
    $("#lighting-counter")
        .countdown("$time", function(event) {
            $(this).text(
                event.strftime('%H:%M:%S')
            );
        });
JS
)
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-4" style="margin-bottom: 60px">
                <div class="timer">
                    До конца акции
                    <div id="lighting-counter"></div>
                    <span class="timer-caption">ЧАСОВ</span>
                    <span class="timer-caption">МИНУТ</span>
                    <span class="timer-caption">СЕКУНД</span>
                </div>
                <hr>
                <h2 style="
                    font-style: italic;
                    font-weight: bold;
                ">Товар дня</h2>
                <p style="margin-bottom: 20px">
                    Каждый день до конца Акции мы будем делать дикие скидки на один из товаров каталога! Подпишись на новости, чтобы ничего не пропустить!
                </p>
                <?php
                $this->registerJs(<<<JS
                $('.toggle-subscribe').on('click', function() {
                    $(this).hide();
                    $('#new-year-subscribe').toggleClass('hidden')
                });

JS
)
                ?>
                <span class="btn btn-default toggle-subscribe" style="font-weight: bold; border-color: #000;">
                    ПОЛУЧАТЬ УВЕДОМЛЕНИЯ
                </span>
                <?php
                $model = new \frontend\models\WinterSaleSubscirbers();
                $form = \yii\widgets\ActiveForm::begin([
                        'action' => '/site/winter-subscriber'
                ]) ?>
                <div id="new-year-subscribe" class="row hidden">
                    <div class="col-xs-8">
                        <?= $form->field($model, 'email')->textInput([
                            'placeholder' => 'Введите ваш e-mail',
                            'class' => 'rad0 form-control',
                            'id' => 'winter-email'
                        ])->label(false) ?>
                    </div>
                    <div class="col-xs-4">
                        <?= \yii\helpers\Html::submitButton('Подписаться', [
                            'class' => 'btn btn-danger',
                        ]) ?>
                    </div>
                    <div class="col-xs-12">
                        <small>
                            Вводя свой E-Mail адрес, вы даете свое согласие на обработку ваших персональных данных, отправку вам уведомлений и другой информации от компании Mad Wave
                        </small>
                    </div>
                </div>
                <?php $form::end() ?>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <?php
                    /**
                     * @var $product \common\models\Product
                     * @var $v \common\models\ProductVariant
                     */
                    $v = (new \common\models\sale\Winter17_18)->getLighingProduct();
                    $product = $v->product;
                    ?>
                    <div class="product clearfix">
                        <div class="row">
                            <div class="col-md-6" style="position: relative">
                                <img src="<?= $v->getImage(1, $v::IMG_LARGE, true, true) ?>"
                                     alt="<?= $product->name ?>"
                                     style="
                                box-shadow: 10px 20px 20px 0 rgba(0,0,0,0.08);
                                width: 100%;
                                position: relative;
                                top: -20px;
                                padding: 30px;
                                background-color: #fff;
                            ">
                            </div>
                            <div class="col-md-6" style="padding-right: 30px">
                                <h2>
                                    <?= $product->name ?>
                                </h2>
                                <div class="percent">
                                    <span>-<?= $product->discount_card_percent ?>%</span>
                                </div>
                                <p>
                                    <?= mb_substr($product->getDescription(), 0, 120, "UTF-8") ?>...
                                </p>
                                <span class="price">
                            <?= $v->getCardPrice() ?><i class="fa fa-rub"></i>
                        </span>
                                <span class="old-price">
                            <?= $v->getOldPrice() ?><i class="fa fa-rub"></i>
                        </span>
                                <div class="size"></div>
                                <div class="color"></div>
                                <a href="<?= $v->getUrl() ?>" class="link btn btn-default">
                                    Перейти к товару
                                </a>
                            </div>
                            <div class="background"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container " style="margin-bottom: 60px">
    <h2 style="
        font-size: 40px;
        color: #000000;
        letter-spacing: 0;
        text-transform: uppercase;
        margin-bottom: 30px;
        margin-top: 40px;
    ">Чаще всего покупают</h2>
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <?php
                $hitProducts = (new ProductSearch())->search([
                    'pageSize' => 8,
                ])->models;

                foreach ($hitProducts as $i => $product) {
                    /* @var $product ProductVariant */
                    echo $product->preview('Home', $i+1, 2);
                }
                ?>
            </div>
        </div>
    </div>
</section>

<div class="container" style="padding-bottom: 60px">
    <div class="row">
        <div class="col-md-8">
            <div style="
                background-color: #ffffff;
                box-shadow: 0 20px 20px 0 rgba(0, 0, 0, 0.06);
                border-top: 7px solid #fa5501;
                padding: 99px 61px 80px;
            ">
                <h2 style="
                    text-align: center;
                    font-weight: bold;
                    margin-top: 0;
                    font-size: 40px;
                    font-style: normal;
                    font-stretch: condensed;
                    line-height: normal;
                    letter-spacing: 0.8px;
                    color: #282828;
                ">ПРАВИЛА АКЦИИ</h2>
                <div style="
                  font-size: 14px;
                  font-weight: normal;
                  font-style: normal;
                  font-stretch: normal;
                  line-height: 1.43;
                  letter-spacing: normal;
                  text-align: center;
                  color: #282828;
                ">
                    <p>Акция «НОВОГОДНИЙ SALE» (далее – «Акция») действительна в период с 20 декабря 2017 года до её отключения Организатором в одностороннем порядке в интернет-магазине «Mad Wave» (www.madwave.ru).</p>
                    <p>В период проведения акции на выбранные товары (далее – «Акционные товары») действует скидка от 0 до 60%. Ассортимент и количество товаров ограничены. Организатор Акции в праве досрочно приостановить действие Акции, отменить ее или возобновить. Организатор вправе в одностороннем порядке изменять условия Акции.</p>
                    <p>Настоящая Акция не суммируется с другими акциями и скидками, кроме скидки 3% при оплате онлайн. В Интернет-магазине Акционные товары доступны для всех способов доставки, в том числе для самовывоза из розничных магазинов, с оплатой при получении. Воспользоваться Акцией при самовывозе можно при условии выкупа заказа в срок до окончания Акции.</p>
                    <p>Ответы на вопросы, не описанные в вышеуказанных условиях, вы можете получить у Специалистов информационной службы, позвонив по номеру: 8-800-100-57-19  (ежедневно c 9:00 до 21:00).</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 text-center">
            <style>
                .soc-share {
                    width: 57px;
                    height: 57px;
                    color: #fff;
                    border-radius: 100%;
                    margin-right: 10px;
                    text-align: center;
                    line-height: 57px;
                    font-size: 16px;
                }
                .soc-share:hover {
                    text-decoration: none;
                    color: #fff;
                    opacity: .8;
                }
                .soc-share:last-child {
                    margin-right: 0px;
                }
            </style>
            <h2 style="
                font-size: 24px;
                font-weight: bold;
                font-style: normal;
                font-stretch: condensed;
                line-height: 1.29;
                letter-spacing: normal;
                text-align: left;
                color: #282828;
                margin-bottom: 20px;
                margin-top: 230px;
            ">РАССКАЗАТЬ ДРУЗЬЯМ ОБ АКЦИИ</h2>
            <?php
            $url = \yii\helpers\Url::canonical();
            ?>
            <a href="http://vk.com/share.php?url=<?= $url ?>" target="_blank" class="soc-share fa fa-vk" style="
                background-color: #4c75a3;
            "></a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $url ?>" target="_blank" class="soc-share fa fa-facebook" style="
                background-color: #425497;
            "></a>
            <a href="https://plus.google.com/share?url=<?= $url ?>" target="_blank" class="soc-share fa fa-google-plus" style="
                background-color: #dd4241;
            "></a>
            <a href="https://twitter.com/intent/tweet?url=<?= $url ?>" target="_blank" class="soc-share fa fa-twitter" style="
                background-color: #00b7ec;
            "></a>
        </div>
    </div>
</div>

<?= PinnedItems::widget() ?>