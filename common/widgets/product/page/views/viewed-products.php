<?php
/**
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $v \common\models\ProductVariant
 */

$string = \Yii::t('app', 'Recently viewed products');
?>
<hr>
<h2 class="text-center mb60"><?= $string ?></h2>
<div class="row">
    <div class="viewed-products">
        <?php foreach ($viewedProducts as  $i => $id) {
            $product_model = \common\models\Product::findOne($id);
            $v = $product_model->getVariantsByColor(1)[0];
            if ($i > 0 && $v) {
                echo $v->preview($string, $i, 2);
            }
        } ?>
    </div>
</div>
