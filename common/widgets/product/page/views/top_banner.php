<?php
/**
 * @var $model \common\models\content\TopBanner
 */
?>

<div class="product-view-banner text-center"
     style="
         background: url('<?= $model->background ?>') center center no-repeat;
         background-size: cover;
         ">

    <h2>
        <?= $model->getTitle() ?>
    </h2>

    <p>
        <?= $model->getText() ?>
    </p>

    <?php if ($url = $model->getVideo()): ?>

        <div class="btn lightbox"
             href="<?= $url ?>">

            <?= Yii::t('app', 'WATCH THE VIDEO') ?> <i class="fa fa-play"></i>

        </div>

    <?php endif; ?>

</div>
