<?php

use common\models\Product;
use common\models\ProductVariant;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;

?>
<div class="container" style="padding-top: 30px">

    <div class="site-error clearfix">

        <style>
            .status-code {
                color: #e9612a;
                font-size: 150px;
                font-style: italic;
                font-weight: bold
            }

            @media (min-width: 900px) {
                .status-code {
                    font-size: 270px;
                }
            }
        </style>
        <div class="row">
            <div class="col-md-6" style="padding-top: 100px">
                <h2 style="font-size: 300%">
                    <b><?= Yii::t('app', 'PAGE NOT FOUND') ?></b>
                </h2>
                <p>
                    <?= Yii::t('app', 'Unfortunately, we can not find the page you requested. You can find the appropriate category by clicking on the links below.') ?>
                </p>
            </div>
            <div class="col-md-6 text-center">
                <span class="status-code">
                    <?= $exception->statusCode ?>
                </span>
            </div>
        </div>



        <?php if (YII_ENV_DEV) { ?>
            <div class="alert alert-warning" style="margin-bottom: 30px !important">
                <b>ЭТО СООБЩЕНИЕ ВИДНО ТОЛЬКО НА ДЕВЕ: </b>
                <?= $name ?>
                <?= $message ?>
            </div>
        <?php } ?>

        <h2><b>ПОДБОРКИ И НОВЫЙ ТОВАР</b></h2>

        <style>
            .cat-plane-404 {
                display: block;
                padding: 15px;
                height: 390px;
                color: #fff;
                font-size: 150%;
                margin-bottom: 15px;
                text-transform: uppercase;
            }
            .cat-plane-404:hover {
                text-decoration: none;
                color: #fff;
            }
        </style>
        <div class="row">
            <div class="col-xs-12 col-md-4">
                <a href="/catalog/tekstil-sportivnoe-plavanie" class="cat-plane-404" style="background: url('/images/layout/matrix.jpg') center center / cover no-repeat">
                    <span><?= Yii::t('app', 'Swimsuits') ?></span>
                </a>
            </div>
            <div class="col-xs-12 col-md-4">
                <a href="/catalog/inventar-dlya-plavaniya/lasty" class="cat-plane-404" style="background: url('/images/layout/lasti.jpg') center center / cover no-repeat">
                    <span><?= Yii::t('app', 'Fins') ?></span>
                </a>
            </div>
            <div class="col-xs-12 col-md-4">
                <a href="/catalog/aksessuary/ochki-dlya-plavaniya" class="cat-plane-404" style="background: url('/images/layout/razer.jpg') center center / cover no-repeat">
                    <span><?= Yii::t('app', 'Goggles') ?></span>
                </a>
            </div>
        </div>




    </div>

</div>