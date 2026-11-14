<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\Collection
 * @var $variant \common\models\ProductVariant
 */

$this->title = $model->getMetaTitle();
$this->registerMetaTag([
    'name' => 'description',
    'content' => $model->getMetaDescription()
]);
?>

<div class="collection-banner" style="background: url('<?= $model->getCover(true, true) ?>') center center / cover no-repeat">
    <div class="container">
        <div class="col-md-6">
            <h2>
                <small>
                    <?= $model->getSubTitle() ?>
                </small>
                <br>
                <?= $model->getTitle() ?>
            </h2>
            <p>
                <?= $model->getDescription() ?>
            </p>
        </div>
    </div>
</div>
<div class="container">
    <div class="clearfix">
        <div class="col-xs-12">
            <h1 style="font-weight: bold; margin-top: 50px"><?= $model->getH1() ?></h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="clearfix">
                    <?php
                    $i = 0;
                    foreach ($model->variants as $variant) {
                        echo $variant->preview('collection - '.\yii\helpers\Html::encode($this->title), $i+1, 2);
                        $i++;
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>
