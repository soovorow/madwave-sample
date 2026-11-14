<?php
/**
 * @var $model \common\models\content\TopBanner
 */

$this->registerJs(<<<JS
var body = $('body');
var pick_result = $('#pick-result')
var delay = (function() {
    var timer = 0;
    return function(callback, ms) {
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

$(function() {
    console.log('The DOM is ready');

    body.on('keyup', '#search-input', function() {
        search($(this).val());
    });
    
     $('.theiaStickySidebarContainer').theiaStickySidebar({
        additionalMarginTop: 90
    });

    body.on('click', '.product-picker', function() {
        $(this).find('input').prop('checked', true);
        pick_result.append($(this).clone())
    });
    
    pick_result.on('click', '.product-picker', function() {
        $(this).remove();
    });
});

function search(val) {
    val = val.toLowerCase();
    var searchResult = $('#search__result');
    searchResult.addClass('loading');
    delay(function(){
        ajaxit('POST', '/top-banner/search', searchResult, {'key': val})
            .done(function (response) {
                searchResult.html(response);
                searchResult.removeClass('loading');
            })
    }, 500 );
}

function ajaxit(type, url, container, data) {
    return $.ajax({
        url: url,
        type: type,
        data: data,
        'beforeSend': function () {
            if (container instanceof HTMLElement) {
                container.addClass('loading')
            }
        },
        'complete': function () {
            if (container instanceof HTMLElement) {
                container.removeClass('loading')
            }
        }
    });
}
JS
)
?>
<style>
    .product-picker {
        cursor: pointer;
    }
    .product-picker:hover {
        border: 1px solid blue;
    }
</style>
<?php $form = \yii\widgets\ActiveForm::begin() ?>

<div class="row">
    <div class="col-xs-12">
        <div class="text-center"
             style="
                 background: url('<?= !empty($model->background) ? $model->background : '#ccc' ?>') center center / cover no-repeat;
                 color: #fff;
                 padding: 50px;
                 margin-bottom: 50px;
             ">
            <h1><?= $model->getTitle() ?></h1>
            <p style="display: block; margin: 0 auto; max-width: 500px"><?= $model->getText() ?></p>
        </div>
    </div>
    <div class="col-xs-12">
        <?= $form->field($model, 'background_file')->fileInput() ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'title_ru') ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'title_en') ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'text_ru')->textarea() ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'text_en')->textarea() ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'video') ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'video_eu') ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'status')->dropDownList([
            0 => 'Черновик',
            1 => 'Опубликовано'
        ]) ?>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-xs-6">
        <label for="search-input">Search</label>
        <input type="text" id="search-input" class="form-control" placeholder="Product search">
        <div id="search__result"></div>
    </div>
    <div class="col-xs-6 theiaStickySidebarContainer">
        <div class="theiaStickySidebar">
            <label>
                Products
            </label>
            <div id="pick-result" class="clearfix">
                <?php if ($items = $model->getProductIds()->all()) {
                    foreach ($items as $item) {
                        if ($v = \common\models\ProductVariant::findOne($item->product->getVariantsByColor()[0])) {
                            ?>
                            <div class="product-picker"
                                 style="overflow: hidden; width: 60px; float:left; margin-right: 10px;">
                                <input type="checkbox" name="TopBanner[product_id_input][<?= $v->product->id ?>]" value="1" hidden checked>
                                <?= $v->getImage(1, $v::IMG_SMALL, false) ?>
                            </div>
                            <?php
                        }
                    }
                } ?>
            </div>
            <hr>
            <div>
                <?= \yii\helpers\Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
</div>


<?php $form::end() ?>