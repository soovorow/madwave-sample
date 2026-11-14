<?php

use common\components\multilocal\Local;

/**
 * @var $v \common\models\ProductVariant
 * @var $model \common\models\Product
 */

$this->registerJsFile('/js/jquery.countdown.min.js', ['depends' => ['yii\web\JqueryAsset']]);
$time = date('Y/m/d', mktime(0,0,0,1,30,2018));
$this->registerJs(<<<JS
    $("#cyber-timer")
        .countdown("$time", function(event) {
            $(this).text(
                event.strftime('%D:%H:%M:%S')
            );
        });
JS
)
?>
<div class="marketing-labels">
    <div class="labels">

        <?php if (Local::detectLocal() == Local::RU) { ?>
            <?php if (!$v->salePrice && $v->product->discount_card !== 1) { ?>
                <div class="label card" style="
                    <?php if (\common\models\sale\CyberMonday::isEnabled()) { ?>
                        background: #000;
                        color: #fff;
                        font-style: italic;
                        border: 1px solid #000;
                    <?php } ?>
                    ">
                    <?php if (!\common\models\sale\CyberMonday::isEnabled()) { ?>
                        <i class="fa fa-credit-card"></i>
                    <?php } ?>
                    <b style="
                    <?php if (\common\models\sale\CyberMonday::isEnabled()) { ?>
                        color: #00f3e9;
                        font-size: 150%;
                        line-height: 40%;
                        position: relative;
                        top: 2px;
                        margin-right: 15px;
                    <?php } ?>
                    ">-<?= $model->getClientCardDiscountPercent() ?>%</b>

                    <?php if (\common\models\sale\CyberMonday::isEnabled()) { ?>
                        <span id="cyber-timer"></span>
                    <?php } else { ?>
                        <?= \Yii::t('app', 'With client card') ?>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>

        <?php if (Local::detectLocal() == Local::RU) { ?>
            <div class="label card">
                <i class="fa fa-credit-card"></i>
                -3% <?= \Yii::t('app', 'When paying online') ?>
            </div>
        <?php } ?>

        <?php if ($model->weekViewsCount > 0) { ?>
            <div class="label" style="background: #fcf8e3;">
                <i class="fa fa-eye"></i>
                <b><?= round($model->weekViewsCount / 2) ?></b>
                <?= Yii::t('app', 'Weekly views') ?>
            </div>
        <?php } ?>

        <?php if (isset($show_product_page_button) && $show_product_page_button === true) { ?>
            <a href="<?= $v->getUrl() ?>"
               class="btn btn-primary label card"
               style="
                background: #00b0cc;
                border-color: #00b0cc;
                color: #fff;
                cursor: pointer;
               ">
                <?= Yii::t('app', 'PRODUCT PAGE') ?>
            </a>
        <?php } ?>
    </div>
</div>