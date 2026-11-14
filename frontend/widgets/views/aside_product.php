<?php
/**
 * @var $v \common\models\ProductVariant
 */
?>
<div class="aside season-sale">
    <p class="title text-center"><?= Yii::t('app', 'Season sale') ?></p>
    <h4 class="text-center">
        <?= $v->product->globalCategory->getName() ?>
        <br>
        <?= $v->getName() ?>
    </h4>
    <div class="img text-center">
        <?= $v->getImage(1, $v::IMG_MEDIUM) ?>
    </div>
    <div class="prices text-center">
        <?= $v->getCardPrice(true) ?>
        <?php if (Yii::$app->language == 'ru-RU') { ?>
            <span class="old">
                <?= $v->getOldPrice(true) ?>
            </span>
        <?php } ?>
    </div>
    <div class="text-center">
        <?= \yii\helpers\Html::a(Yii::t('app', 'Buy'), $v->getUrl(), ['class' => 'btn btn-lg btn-danger']) ?>

    </div>
</div>