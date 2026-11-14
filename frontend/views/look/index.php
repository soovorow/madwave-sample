<?php
/**
 * @var $look \common\models\TheLook
 * @var $model \common\models\TheLook
 * @var $dataProvider \yii\data\ActiveDataProvider
 */

use frontend\widgets\ShopTheLookSlider;

$this->registerJs(<<<JS
    $('body').on('click', '[data-loadview]', function () {
        var container =  $(this).parents('#variant');
        $.ajax({
            'type': 'post',
            'url': '/ajax/load-new-view',
            'data': {'sku': $(this).data('loadview')},
            'beforeSend': function () {container.addClass('loading')},
            'complete': function () {container.removeClass('loading')},
            'success': function (response) {
                var r = JSON.parse(response);
                container.html($(r.view));
                window.history.pushState(null,'hi','?sku='+r.sku);
                zoom();
                ga('send', 'pageview', window.location.href );
            }
        })
    })

JS
);
?>




<section style="
    padding-top: 60px;
    padding-bottom: 100px;
">
    <div class="container">
        <div class="row">
            <div class="col-xs-12" style="padding-top: 30px;">
                <h1 class="text-center" style="margin-bottom: 50px; margin-top: -50px;">SHOP THE LOOK</h1>

                <?= ShopTheLookSlider::widget([
                    'looks' => $looks,
                    'model' => $model
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 text-center" style="margin-top: 130px">
                <h1 style="margin-top: 0; text-transform: uppercase">
                    <span style="
                font-size: 42px; ;
                font-weight: bold;
                font-stretch: condensed;">
                    <?= $model->getTitle() ?></span>
                </h1>
            </div>
            <div class="col-xs-6 col-xs-push-3 text-center">
                <p>
                    <?= $model->getDescription() ?>
                </p>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="row">
        <?php if ($vars = $model->getVariants(true)) {
            foreach ($vars as $v) {
                /**
                 * @var $v \common\models\ProductVariant
                 */
                ?>
                <div id="variant" class="ajax-loading">
                    <?= $this->render('../catalog/_view_new', [
                        'v' => $v,
                        'model' => $v->product,
                        'show_product_page_button' => true
                    ]); ?>
                </div>
         <?php
            }
        } ?>
    </div>
</div>