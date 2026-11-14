<?php
/** @var $model \frontend\widgets\ui\ProductPromoSection */
?>

<section class="feature" style="background: url('/images/new_features/<?= $model->image ?>') center center / cover no-repeat">
    <div class="container">
        <div class="col-xs-12 col-md-6 <?= $model->float == 'right' ? 'col-md-push-6' : '' ?>">
            <h2><?= $model->title ?></h2>
            <p><?= $model->desc ?></p>
        </div>
    </div>
</section>