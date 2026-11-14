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
        ?>
        <div>
            <div class="clearfix">
                <?php foreach ($variants as $v) { ?>
                    <div class="product-picker" style="overflow: hidden; width: 60px; float:left; margin-right: 10px;">
                        <input type="checkbox" name="Collection[sku_input][<?= $v->variant_id ?>]" value="1" hidden>
                        <?= $v->getImage(1, $v::IMG_SMALL, false) ?>
                    </div>
                <?php } ?>
            </div>

            <p><b><?= $p->name ?></b></p>
            <p><?= $p->globalCategory->getName() ?></p>
        </div>
        <hr>
    <?php  }
} ?>