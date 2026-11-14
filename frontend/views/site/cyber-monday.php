<?php

use common\models\content\PinnedItems;
use common\models\ProductVariant;
use common\models\SelfPickUpPoints;
use frontend\models\ProductSearch;
use frontend\modules\citydetector\CityDetector;

$w = new \common\models\sale\Winter17_18();
//$w->enableDiscounts();
//$w->productRotation();
$this->title = 'КИБЕР-ПОНЕДЕЛЬНИК С MAD WAVE';
$this->registerMetaTag(['name'=>'description', 'content' => 'Скидки до 50% на ВСЕ товары магазина!']);

?>
<?php
    $this->registerJsFile('/js/jquery.countdown.min.js', ['depends' => ['yii\web\JqueryAsset']]);
    $time = date('Y/m/d', mktime(0,0,0,1,30,2018));
    $this->registerJs(<<<JS
    $("#counter")
        .countdown("$time", function(event) {
            $(this).text(
                event.strftime('%D:%H:%M:%S')
            );
        });
JS
    )
?>
<style>
    .banner.falksjda {
        height: 300px;
    }
    @media (min-width: 900px) {
        .banner.falksjda {
            height: 450px;
        }
    }
</style>
<section style="background: #000; margin-bottom: 60px">
    <div class="container">
        <div id="counter" class="text-center" style="
                    font-size: 60px;
                    margin-top: 170px;
                    margin-bottom: -170px;
                    color: #00f3e9;
                    position: relative;
                    z-index: 1;
                "></div>
        <div class="banner text-center"
             style="background:url(/special/cyber-monday/kiber.gif) center center / contain no-repeat;
                 height: 550px;
                 padding-top: 400px;
                 position: relative;
            ">

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
    "><b>БЕСТСЕЛЛЕРЫ</b></h2>
        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <?php
                    $skus = [
                        '10019049',
                        '10022333',
                        '10021523',
                        '10021858',
                        '10020197',
                        '10022588',
                        '10015972',
                        '10022905',
                    ];

                    foreach ($skus as $sku) {
                        /* @var $v ProductVariant */
                        $v = ProductVariant::findOne($sku);
                        echo $v->preview('Home', $i+1, 2);
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

<div class="mb60">

    <?= $this->render('_pinned-categories') ?>
</div>

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
                        <p>Акция «КИБЕР-ПОНЕДЕЛЬНИК» (далее – «Акция») действительна в период с 18:00 28 января 2018 года до 23:59 29 января 2018 года в интернет-магазине «Mad Wave» (www.madwave.ru). Начало дня исчисляется с 00:00 по МСК.</p>

                        <p>В период проведения акции на выбранные товары (далее – «Акционные товары») действует скидка от 0 до 50%. Ассортимент и количество товаров ограничены. Организатор Акции в праве досрочно приостановить действие Акции, отменить ее или возобновить. Организатор вправе в одностороннем порядке изменять условия Акции.</p>

                        <p>Настоящая Акция не суммируется с другими акциями и скидками, кроме скидки 3% при оплате онлайн. В Интернет-магазине Акционные товары доступны для всех способов доставки, в том числе для самовывоза из розничных магазинов, с оплатой при получении. Воспользоваться Акцией при самовывозе можно при условии выкупа заказа в срок до окончания Акции.</p>

                        <p>Ответы на вопросы, не описанные в вышеуказанных условиях, вы можете получить у Специалистов информационной службы, позвонив по номеру: 8-800-100-57-19 (ежедневно c 9:00 до 21:00).</p>
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


<div class="container mb60">

    <?php
    $city = CityDetector::getCity();

    $stores = SelfPickUpPoints::find()->where(['id' => [
        21,
        6,
        4,
        7,
        25,
        3,
        11
    ]]);

    $stores_result_view = $this->render('/site/stores__result', [
        'stores' => $stores,
    ]);

    $stores_all_view = $this->render('/site/stores__all', [
        'stores' => $stores,
        'stores_array' => $stores->asArray()->orderBy('city')->all(),
    ]);
    ?>
    <div class="product-view row mb60" style="padding-top: 0;">
        <div class="ajax-loading" id="variant">
            <div class="clearfix">
                <div class="col-xs-12 top-caption">
                    <h2 class="text-center" style="margin-top: 15px">
                        <b>МАГАЗИНЫ, УЧАСТВУЮЩИЕ В АКЦИИ</b>

                    </h2>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="col-xs-12 " style="
border: 1px solid #ccc">
                    <div class="row">
                        <?= $stores_all_view ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

