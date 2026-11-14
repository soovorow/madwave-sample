<?php
/**
 * @var $p \common\models\Product
 * @var $v \common\models\ProductVariant
 * @var $productQuery \yii\db\ActiveQuery
 */
if ($productQuery) { ?>
    <h3><?= Yii::t('app', 'Products') ?></h3>
    <?php foreach ($productQuery as $p) {
        $variants = $p->variants;
        $v = $p->getVariantsByColor()[0];
        ?>
        <div>
            <div class="clearfix">
                <div class="product-picker" style="overflow: hidden; width: 60px; float:left; margin-right: 10px;">
                    <input type="checkbox" name="TopBanner[product_id_input][<?= $p->id ?>]" value="1" hidden>
                    <?= $v->getImage(1, $v::IMG_SMALL, false) ?>
                </div>
            </div>

            <p><b><?= $p->name ?></b></p>
            <p><?= $p->globalCategory->getName() ?></p>
        </div>
        <hr>
    <?php  }
} ?>