<?php
/**
 * @var $model \common\models\TheLook
 */

use yii\helpers\Html;

$this->registerJs(<<<JS
var body = $('body');
var pick_result = $('#pick-result');
var delay = (function() {
    var timer = 0;
    return function(callback, ms) {
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

$(function() {
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
    });
    
    var productPickerWraps = pick_result.find('.product-picker-wrap');
    productPickerWraps.each(function() {
        var wrap = $(this);
        var picker = wrap.find('.product-picker');
        picker.on('click', function() {
            wrap.remove();
        });
        var btnUp = wrap.find('.btn-move-up');
        btnUp.on('click', function(ev) {
            ev.preventDefault();
            if (wrap.prev()) {
                wrap.insertBefore(wrap.prev());
            }
        });
        var btnDown = wrap.find('.btn-move-down');
        $(btnDown).on('click', function(ev) {
            ev.preventDefault();
            if (wrap.next()) {
                wrap.insertAfter(wrap.next());
            }
        });
    });
    
    $('#the-look-form').on('submit', function() {
        productPickerWraps.each(function() {
            var posInput = $(this).find('.product-picker-pos-input');
            posInput.attr('value', $(this).index());
        });
    });
});

function search(val) {
    val = val.toLowerCase();
    var searchResult = $('#search__result');
    searchResult.addClass('loading');
    delay(function(){
        ajaxit('POST', '/shop-the-look/search', searchResult, {'key': val})
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
<?php $form = \yii\widgets\ActiveForm::begin(['id' => 'the-look-form']) ?>

<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'img')->fileInput() ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'status')->dropDownList([
            0 => 'Draft',
            1 => 'Published'
        ]) ?>
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
                <?php if ($items = $model->getSku()->orderBy('pos')->all()) {
                    foreach ($items as $item) {
                        if ($v = \common\models\ProductVariant::findOne($item->sku)) {
                            ?>
                            <div class="product-picker-wrap text-center"
                                 style="overflow: hidden; width: 60px; float: left; margin-right: 10px;">
                                <div class="product-picker">
                                    <label style="font-size: 10px; text-align: center;"><?= $v->variant_id?>
                                        <input type="checkbox" name="TheLook[sku_input][<?= $v->variant_id ?>]"
                                               value="1" hidden checked>
                                    </label>
                                    <label>
                                        <input class="product-picker-pos-input" type="number"
                                               name="TheLook[sku_pos][<?= $v->variant_id ?>]" value="0" hidden>
                                    </label>
                                    <?= $v->getImage(1, $v::IMG_SMALL, false) ?>
                                </div>
                                <?= Html::button('<b><</b>', ['type' => 'button', 'class' => 'btn-move-up', 'style' => 'padding: 0 2px; line-height: 1em; margin-top: 5px;']) ?>
                                <?= Html::button('<b>></b>', ['type' => 'button', 'class' => 'btn-move-down', 'style' => 'padding: 0 2px; line-height: 1em;']) ?>
                            </div>
                            <?php
                        }
                    }
                } ?>
            </div>
            <hr>
            <div>
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
</div>


<?php $form::end() ?>