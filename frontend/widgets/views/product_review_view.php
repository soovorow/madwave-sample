<?php

/**
 * @var $tt \common\models\Testimonials
 */

use common\components\DateTimeFormatter;
use common\models\ProductVariant;
use common\widgets\product\review\ReviewRate;
use frontend\widgets\Rating;

;
?>
<div class="testimonials clearfix" data-review_id="<?= $tt->id ?>">

    <div class="row">
        <div class="col-sm-3">
            <h4><?= $tt->name ?></h4>
            <div><?= DateTimeFormatter::dateAsString($tt->created_at) ?></div>
            <div>
                <h5><?= Yii::t('app', 'Rating') ?></h5>
                <?= Rating::widget(['rating' => $tt->rating]) ?>
            </div>
            <?= $tt->quantity ? ReviewRate::widget([
                'label' => Yii::t('app', 'Quality'),
                'active' => $tt->quantity,
                'caption' => [Yii::t('app', 'Low'), Yii::t('app', 'High')],
                'options' => ['class' => 'mb20']
            ]) : null ?>
            <?php
            $size = null;
            if ($ttv = ProductVariant::findOne(['variant_id' => $tt->size])) {
                $size = $ttv->size->name;
            }
            ?>

            <?php if (count($model->getVariantsBySizes(null, $v->color_id)) > 0 && !empty($model->getVariantsBySizes(null, $v->color_id)[0]['size_id'])) { ?>
            <?= $tt->size_match ? ReviewRate::widget([
                'label' => 'Размер <span style="color: #aaa">'.$size.'</span>',
                'active' => $tt->size_match,
                'caption' => [ Yii::t('app', 'Too small'), Yii::t('app', 'Too big')],
                'options' => ['class' => 'mb20']
            ]) : null ?>
            <?php } ?>
        </div>
        <div class="col-sm-9 testimonials-post">
            <div style="background: #f9f9f9; color: #808080; padding: 35px 30px; font-weight: 500; font-size: 16px">
                <p style="margin-bottom: 30px"><i class="fa custom-plus">+</i><?= $tt->getPros() ?></p>
                <p style="margin-bottom: 30px"><i class="fa custom-minus">-</i><?= $tt->getCons() ?></p>
                <p style="margin-bottom: 30px"><i class="fa fa-comment-o"></i><?= $tt->getText() ?></p>

                <div class="vote-container ajax-loading">
                    <div class="vote vote-up <?= $vote && $vote->value == 1 ? 'active' : 'locked' ?>"
                         data-vote_for="place"
                         data-vote="true"><i class="fa fa-thumbs-up"></i><span><?= $tt->votes_up !== null ? $tt->votes_up : 0?></span></div>
                    <div class="vote vote-down <?= $vote && $vote->value == 0 ? 'active' : 'locked' ?>"
                         data-vote_for="place"
                         data-vote="false"><i class="fa fa-thumbs-down"></i><span><?= $tt->votes_down !== null ? $tt->votes_down : 0?></span></div>
                </div>
                <div class="clearfix">
                    <div class="alert alert-warning alert-dismissable">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <?= Yii::t('app', 'You need to be authorized user to vote for this comment') ?>
                    </div>
                </div>

                <?php if (!empty($tt->reply)) { ?>
                <hr>
                <blockquote>
                    <p>
                        Cотрудник компании Mad Wave.
                        <span class="pull-right"><?= DateTimeFormatter::dateAsString($tt->updated_at) ?></span>
                    </p>
                    <small><?= $tt->reply ?></small>
                </blockquote>
                <?php } ?>
            </div>
        </div>
    </div>

<!---->
<!--    <blockquote>-->
<!--        <p>-->
<!--            <small>-->
<!--                --><?php //if (!\Yii::$app->user->isGuest && $tt->user_id == \Yii::$app->user->identity->getId()) { ?>
<!--                    <span class="a edit-review" data-review-id="--><?//= $tt->id ?><!--">редактировать</span>-->
<!--                --><?php //} ?>
<!--            </small>-->
<!--        </p>-->
<!--    </blockquote>-->
</div>
<hr>