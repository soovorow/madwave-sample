<?php
/**
 * @var $rating integer
 * @var $raring_widget string
 * @var $review_count_string string
 * @var $total_quality_widget string
 * @var $total_size_much_widget string
 * @var $testimonialsProvider \yii\data\ActiveDataProvider
 */
use frontend\widgets\ProductReviewView;

?>
<div class="variant-tabs">

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation"
            class="active">
            <a href="#reviews"
               aria-controls="reviews"
               role="tab"
               data-toggle="tab">
                <?= \Yii::t('app', 'Customer reviews') ?>
            </a>
        </li>
    </ul>
    <div class="tab-content">

        <div role="tabpanel" class="tab-pane active row" id="reviews">
            <div class="col-xs-12">
                <div class="rating-overview">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="rating-overview__rating"><?= $rating ?></div>
                            <div class="color-primary">
                                <?= $raring_widget ?>
                            </div>
                            <p>
                                <?= $review_count_string ?>
                            </p>
                        </div>

                        <div class="col-md-9">
                            <h3><?= Yii::t('app', 'Sort by parameters') ?></h3>
                            <br>
                            <div class="row">
                                <?= $total_quality_widget ?>
                                <?= $total_size_much_widget ?>
                            </div>
                        </div>

                    </div>
                </div>


                    <div class="product-reviews">
                        <?php $isEmpty = $testimonialsProvider->totalCount == 0; ?>

                        <?php if (!$isEmpty) { ?>
                            <h2 id="review" class="text-uppercase">
                                <?= \Yii::t('app', 'The most useful review') ?>
                                <button type="button" class="btn btn-danger btn-write-review">
                                    <?= \Yii::t('app', 'Write review') ?>
                                </button>
                            </h2>
                        <?php } ?>

                        <div class="clearfix">
                            <?php if (!$isEmpty) {
                                foreach ($testimonialsProvider->models as $tt) {
                                    echo ProductReviewView::widget([
                                        'tt' => $tt,
                                        'model' => $model,
                                        'v' => $v
                                    ]);
                                }
                                echo \yii\bootstrap\Html::tag('div', \yii\widgets\LinkPager::widget([
                                    'pagination' => $testimonialsProvider->pagination
                                ]), ['class' => 'text-center']);

                            } ?>


                            <?= \common\widgets\product\review\Form::widget([
                                'testimonialsForm' => $testimonialsForm,
                                'model' => $model,
                                'v' => $v,
                                'isEmpty' => $isEmpty
                            ]) ?>
                        </div>
                    </div>

            </div>
        </div>

    </div>

</div>