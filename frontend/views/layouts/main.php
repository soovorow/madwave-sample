<?php

use common\components\multilocal\Local;
use yii\helpers\Html;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use \yii\widgets\Spaceless;

/**
 * @var $this \yii\web\View
 * @var $content string
 */



$app = \Yii::$app;
$controller = $app->controller;

AppAsset::register($this);
Spaceless::begin();
$this->beginPage(); ?>

<!DOCTYPE html>
<html lang="<?= $app->language ?>">
<head>
    <meta charset="<?= $app->charset ?>"/>
    <title><?= Html::encode($this->title) ?></title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <?= Html::csrfMetaTags() ?>
    <?php

    $params = Yii::$app->controller->actionParams;
    $params[0] = Yii::$app->controller->getRoute();
    $canonical = Yii::$app->urlManager->createAbsoluteUrl($params, 'https');

    if (count(explode('?', $canonical)) > 1) {
        $canonical = explode('?', $canonical)[0];
    }

    if (count(explode('#', $canonical)) > 1) {
        $canonical = explode('#', $canonical)[0];
    }

    ?>

    <?php
    $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $part = explode('?', $actual_link);
    $canon = isset($part[0]) ? $part[0] : $actual_link;
    ?>
    <?= $this->registerLinkTag(['rel' => 'canonical', 'href' => $canon]) ?>

    <?php

    $domain = $_SERVER['SERVER_NAME'];

    if (YII_ENV_PROD && Local::detectLocal() == Local::RU) {
        $explode = explode('//', $canonical);

        $url = [
            $explode[0],
            '//',
            substr($explode[1], 0, 3) === 'www' ? '' : 'www.',
            $explode[1]
        ];

        if (isset(explode('?',  Url::current())[1])) {
            $url[] = '?'.explode('?',  Url::current())[1];
        }


        if (substr($domain, 0, 3) !== 'www') {

            Yii::$app->controller->redirect(implode($url), 301);
        }
    }

    if (YII_ENV_PROD && Local::detectLocal() == Local::EU) {
        $explode = explode('//', $canonical);
        $url = [
            $explode[0],
            '//',
            substr($explode[1], 4)
        ];

        if (substr($domain, 0, 3) === 'www') {
            Yii::$app->controller->redirect(implode($url), 301);
        }
    }
    ?>

    <?php $this->head() ?>

    <link rel="stylesheet" type="text/css" href="/css/style.min.css?<?= @filectime(\Yii::getAlias('@webroot').'/css/style.min.css') ?>"/>

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=zXrA6EalEG">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=zXrA6EalEG">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=zXrA6EalEG">
    <link rel="manifest" href="/manifest.json?v=zXrA6EalEG">
    <link rel="mask-icon" href="/safari-pinned-tab.svg?v=zXrA6EalEG" color="#5bbad5">
    <link rel="shortcut icon" href="/favicon.ico?v=zXrA6EalEG">
    <meta name="theme-color" content="#ffffff">
    <meta name="p:domain_verify" content="5dfa4f6db88e4e44b7a85a0156689216"/>

    <?php if (YII_ENV === 'prod') { ?>
        <link rel="manifest" href="/manifest.json">
        <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async></script>
        <script>
            var OneSignal = window.OneSignal || [];
            OneSignal.push(["init", {
                appId: "50070cb4-51e0-4867-822a-f49aedbcfabf",
                autoRegister: true,
                notifyButton: { enable: false },
                welcomeNotification: { disable: true }
            }]);
        </script>
    <?php } ?>

    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700&amp;subset=cyrillic" rel="stylesheet">
</head>
<body class="<?= $controller->id.' '.$controller->action->id ?>">
<?php $this->beginBody() ?>

    <div id="wrap" style="<?= Local::choose([Local::EU => 'margin: 0 auto -206px;']) ?>">

        <?= Alert::widget() ?>

        <?= $this->render(\common\components\multilocal\Local::choose([
            'ru-RU' => '_subheader-ru',
            'en-EU' => '_subheader-en'
        ])) ?>

        <?= $this->render('_header') ?>

        <div id="headerBuffer"></div>

        <?php if (isset($this->params['breadcrumbs'])) { ?>
            <div class="breadcrumbs_container">
                <div class="container">
                    <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs'],]) ?>
                </div>
            </div>
        <?php } ?>

        <?= $content ?>
    </div>

    <?= $this->render('_footer') ?>


    <?php if (\common\components\multilocal\Local::detectLocal() == \common\components\multilocal\Local::RU) { ?>
        <!-- Global site tag (gtag.js) - Google AdWords: 941159415 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-941159415"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'AW-941159415');
        </script>

        <script>
            //jivosite
            (function() {
                var widget_id = 'qniXGFdFLV';
                var d = document;
                var w = window;
                function l() {
                    var s = document.createElement('script');
                    s.type = 'text/javascript'; s.async = true;
                    s.src = '//code.jivosite.com/script/widget/'+widget_id;
                    var ss = document.getElementsByTagName('script')[0];
                    ss.parentNode.insertBefore(s, ss);
                }
                if(d.readyState === 'complete') {
                    l();
                } else {
                    if(w.attachEvent) {
                        w.attachEvent('onload', l);
                    } else {
                        w.addEventListener('load', l, false);
                    }
                }
            })();

            //yandex metric
            (function (d, w, c) {
                (w[c] = w[c] || []).push(function() {
                    try {
                        w.yaCounter22526140 = new Ya.Metrika({
                            id:22526140,
                            clickmap:true,
                            trackLinks:true,
                            accurateTrackBounce:true,
                            webvisor:true,
                            ecommerce:"dataLayer"
                        });
                    } catch(e) { }
                });
                var n = d.getElementsByTagName("script")[0],
                    s = d.createElement("script"),
                    f = function () { n.parentNode.insertBefore(s, n); };
                s.type = "text/javascript";
                s.async = true;
                s.src = "https://mc.yandex.ru/metrika/watch.js";
                if (w.opera === "[object Opera]") {
                    d.addEventListener("DOMContentLoaded", f, false);
                } else { f(); }
            })(document, window, "yandex_metrika_callbacks");
            window.dataLayer = window.dataLayer || [];

            // GOOGLE ANALYTICS
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
            ga('create', 'UA-35217976-1', 'auto');
            ga('require', 'ec');
            ga('set', 'currencyCode', 'RUB');
        </script>
    <?php } else { ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-82641409-1"></script>
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
            ga('create', 'UA-82641409-1', 'auto');
            ga('require', 'ec');
            ga('set', 'currencyCode', 'EUR');
        </script>
    <?php } ?>


    <?php $this->endBody() ?>

    <script src="https://maps.googleapis.com/maps/api/js?<?= \Yii::$app->params['googleMapJs'] ?>"></script>

    <script>ga('send', 'pageview');</script>

    <script>
        (function(yourcode) {
            yourcode(window.jQuery, window, document);
        }(function($, window, document) {
            $(function() {

                $('#headerBuffer').css('height', $('#header').height());
                console.log(123);

            });
        }));
    </script>

</body>
</html>
<?php $this->endPage(); ?>
<?php Spaceless::end(); ?>

