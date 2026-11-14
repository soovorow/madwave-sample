<?php
/**
 * @var $model \common\models\Collection
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
        ajaxit('POST', '/collections/search', searchResult, {'key': val})
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
        <?= $form->field($model, 'img')->fileInput() ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'slug') ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'slug_eu') ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'subtitle') ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'subtitle_eu') ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'title') ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'title_eu') ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'description') ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'description_eu') ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'meta_title') ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'meta_title_eu') ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'meta_description') ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'meta_description_eu') ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'h1') ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'h1_eu') ?>
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
                <?php if ($items = $model->getSku()->all()) {
                    foreach ($items as $item) {
                        if ($v = \common\models\ProductVariant::findOne($item->sku)) {
                            ?>
                            <div class="product-picker"
                                 style="overflow: hidden; width: 60px; float:left; margin-right: 10px;">
                                <input type="checkbox" name="Collection[sku_input][<?= $v->variant_id ?>]" value="1" hidden checked>
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