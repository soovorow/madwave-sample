<?php

use common\models\Product;

/**
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $v \common\models\ProductVariant
 */

$relatedProductString = \Yii::t('app', 'Related products');
?>
<h2 class="text-center mb60"><?= $relatedProductString ?></h2>
<div class="row">
    <div class="related-products">
        <?php foreach ($dataProvider->models as  $i => $rp) {
            $product_model = Product::findOne($rp->related_product_id);
            $v = $product_model->getVariantsByColor(1)[0];
            echo $v->preview($relatedProductString, $i, $size);
        } ?>
    </div>
</div>
