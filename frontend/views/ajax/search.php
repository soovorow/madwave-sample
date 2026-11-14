<?php

use yii\helpers\Html;

/**
 * @var $catQuery \yii\db\ActiveQuery
 * @var $productQuery \yii\db\ActiveQuery
 * @var $key string
 */

if ($catQuery) { ?>

    <h3><?= Yii::t('app', 'Categories') ?></h3>
    <?php foreach ($catQuery as $id => $c) {
        /** @var $c \common\models\LocalCategory */
        $linkContent = $c->getHeader().Html::tag('span', $c->getTitle());
        echo Html::tag('a', $linkContent, [
            'class' => 'category__header search  mb20',
            'href' => $c->getUrl()
        ]);
    }
}

if ($productQuery) { ?>
    <h3><?= Yii::t('app', 'Products') ?></h3>
    <?php foreach ($productQuery as $id => $p) {
        /** @var $p \common\models\ProductVariant */

        $product = $p->product;
        $hasAnyInStock = $p->hasAnyInStock();
                $imageUrl = $p->getImage(1, $p::IMG_SMALL, true, true);

        ?>
        <a href="<?= $p->getUrl() ?>" class="clearfix search-product-card">
            <div class="image <?= $hasAnyInStock ? '' : 'out-of-stock' ?>" style="background: url('<?= $imageUrl ?>') center center / cover no-repeat;"></div>
            <p><?= $p->getName().($hasAnyInStock ? '' : Html::tag('span', Yii::t('app', 'out of stock'), ['class' => 'pull-right'])) ?></p>
            <p><?= $product->globalCategory->getName() ?></p>
            <p>
                <?php if ($p->salePrice || $product->discount_card == 1) { ?>
                    <small class="fsize-1 price"><?= $p->getPrice(true) ?></small>
                <?php } else { ?>
                    <small class="fsize-1 price <?= ($hasAnyInStock ? 'color-primary' : '') ?>"
                           style="<?= (!$hasAnyInStock ? 'color: #9a9a9a' : '') ?>">
                        <?= $p->getCardPrice(true) ?>
                        <span class="pull-right">
                            -<?= ($product->discount_card_percent > 10 ? $product->discount_card_percent : 10) ?>%
                            <i class="fa fa-credit-card"></i>
                        </span>
                    </small>
                <?php } ?>
            </p>
        </a>
        <?php
    }
    ?>

    <?= Html::a(Yii::t('app', 'More'), [
        '/catalog/search', 'keywords' => Html::encode($key)
    ], [
        'class' => 'btn btn-primary w100 mb30'
    ]) ?>

<?php }

if (!$catQuery && !$productQuery) { ?>
    <h3><?= Yii::t('app', 'Not found') ?></h3>
    <?= $this->render('../layouts/search-empty-state') ?>
<?php } ?>
