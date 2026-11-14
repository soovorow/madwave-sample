<?php

use common\components\multilocal\Local;
use common\models\Product;
use common\models\ProductVariant;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * @var $this yii\web\View
 * @var $model \common\models\Product
 *
 * @var $category \common\models\LocalCategory
 *
 * @var $related_products string
 * @var $viewed_products string
 * @var $product_reviews string
 *
 */


$this->registerMetaTag(['name' => 'description', 'content'  => '']);

$category_title = '';
if ($category = \common\models\LocalCategory::findOne(21)) {
    if ($parent = $category->getParent()) {
        $this->params['breadcrumbs'][] = ['label' => $parent->getTitle(), 'url' => $parent->getUrl()];
    }
    $this->params['breadcrumbs'][] = ['label' => $category->getTitle(), 'url' => $category->getUrl()];
    $category_title = ' | ' . $category->getTitle();
}
$this->params['breadcrumbs'][] = 'STREAMLINE OPTICAL RAINBOW';

$title = 'STREAMLINE' . $category_title . ' | ' . 'Mad Wave';

$this->title = $title;

$this->registerJs(<<<JS
    $('body').on('click', '[data-loadview]', function () {
        var container =  $('#variant');
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

<div class="product-view-banner text-center" style="background: url('/images/new_features/streamline_bunner.jpg') center center no-repeat; background-size: cover;">
    <h2>STREAMLINE OPTICAL RAINBOW</h2>
    <p><?= Yii::t('app', 'High-technology goggles with perfected hydrodynamic features and low-profile design now with wide range of
diopters for the highest level of comfort at starts.') ?></p>
</div>

<div class="container" >
    <div class="product-view row mb60">
        <div id="variant" class="ajax-loading">
            <?php


            $currency = \Yii::$app->params['currency']

            ?>
            <div class="clearfix">

                <div class="col-xs-12">
                    <div class="top-caption">
                        <h1 itemprop="name">Streamline Optical Rainbow</h1>
                    </div>
                    <div class="marketing-labels">
                        <div class="labels">


                            <?php if (Local::detectLocal() == Local::RU) { ?>
                                <div class="label card">
                                    <i class="fa fa-credit-card"></i>
                                    -3% <?= \Yii::t('app', 'When paying online') ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6 col-xs-12 p0">

                    <div class="col-xs-12">

                        <?php $v = ProductVariant::findOne(10021351) ?>

                        <div class="view_img">
                            <img id="zoom"
                                 itemprop="image"
                                 width="100%"
                                 alt="<?= $v->product->globalCategory->getName(). ' ' . $v->name  ?>"
                                 src="<?= $v->getImage(1, $v::IMG_LARGE, true, true) ?>"
                                 data-zoom-image="<?= $v->getImage(1, $v::IMG_ORIGINAL, true, true) ?>" />

                        </div>

                        <div class="all_imgs clearfix">
                            <?php for ($i = 1; $i <= 4; $i++) {
                                echo $v->getImage($i, $v::IMG_SMALL);
                            } ?>
                        </div>

                    </div>

                </div>

                <div id="product-description-width" class="col-sm-6 col-md-8 col-xs-12"></div>

                <div class="col-md-8 col-xs-12">
                    <div class="row">
                        <div class="col-md-5 col-md-push-7 col-sm-6 col-xs-12  ">

                            <?php
                            $v = ProductVariant::findOne(10021575);
                            $leftLens = Product::findOne(1375);
                            $rightLens = Product::findOne(1359);
                            $rlp = $rightLens->getVariantsBySizes(1)[0];

                            $llp = $leftLens->getVariantsBySizes(1)[0];

                            ?>

                            <div class="col-xs-12 cleaerfix add-to-cart-container">
                                <div class="col-xs-12">
                                    <div class="row" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                                        <meta itemprop="priceCurrency" content="RUB" />
                                        <b class="titled cost"><?= \Yii::t('app', 'Price') ?></b>

                                        <?php
                                        $price = $v->price + $rlp->price + $llp->price;
                                        $cardPrice = $v->cardPrice + $rlp->cardPrice + $llp->cardPrice;
                                        ?>
                                        <?php if (Local::detectLocal() == Local::RU) { ?>

                                            <span class="price">
                                                <?= number_format($price, 0, '.', ' ') . $currency ?>
                                            </span>
                                            <span class="target-price">
                                                <span class="color-primary">
                                                    <i class="fa fa-credit-card"></i>
                                                    <span itemprop="price"><?= number_format($cardPrice, 0, '.', ' ') . $currency ?></span>
                                                </span>
                                            </span>

                                        <?php } else { ?>

                                            <span class="target-price">
                                                    <span itemprop="price"><?= number_format($price, 0, '.', ' ') . $currency ?></span>
                                                </span>

                                        <?php } ?>


                                    </div>

                                    <?php if (Local::detectLocal() == Local::RU && (!$v->salePrice && $v->product->discount_card != 1)) { ?>
                                        <div class="row">
                                            <b class="titled cost"><?= \Yii::t('app', 'Discount with card') ?></b>
                                            <span class="card-discount">
                                                <?= number_format(($v->price - $v->cardPrice), 0, '.', ' ') . $currency ?>
                                            </span>
                                        </div>
                                    <?php } ?>

                                    <div class="row">
                                        <div class="col-md-6" style="padding: 0">
                                            <?php
                                            $leftItems = [];
                                            foreach ($leftLens->getVariantsBySizes() as $vsize) {
                                                /**
                                                 * @var $vsize ProductVariant
                                                 */
                                                $leftItems[$vsize->variant_id] = $vsize->size->name;
                                            }


                                            echo \yii\helpers\Html::dropDownList('left-lense', null, $leftItems, [
                                                'prompt' => Yii::t('app', 'Left Lens'),
                                                'style' => 'width: 100%'
                                            ]);
                                            ?>
                                            <div class="left-lense help-block"></div>
                                        </div>
                                        <div class="col-md-6" style="padding: 0">
                                            <?php

                                            $rightItems = [];
                                            foreach ($rightLens->getVariantsBySizes() as $vsize) {
                                                /**
                                                 * @var $vsize ProductVariant
                                                 */
                                                $rightItems[$vsize->variant_id] = $vsize->size->name;
                                            }


                                            echo \yii\helpers\Html::dropDownList('right-lense', null, $rightItems, [
                                                'prompt' => Yii::t('app', 'Right Lens'),
                                                'style' => 'width: 100%'
                                            ]);

                                            ?>
                                            <div class="right-lense help-block"></div>

                                        </div>
                                    </div>

                                    <div class="row">
                                        <?php

                                        $quantity = [];
                                        for ($o = 1; $o <= 20; $o++) {
                                            $quantity[$o] = $o;
                                        }

                                        $input = Html::dropDownList('quantity', 1, $quantity, [
                                            'id' => 'add_to_cart-quantity',
                                            'class' => 'form-control',
                                            'style' => 'width: 100%;',
                                        ]);
                                        $field = Html::tag('b', \Yii::t('app','Quantity'), ['class'=>'titled']) . Html::tag('div', $input, ['class' => 'mb30' . ($hidden_input ? ' hidden' : '')]);
                                        $btn = Html::tag(
                                            'span',
                                            ($btn_value ? $btn_value : (\Yii::t('app','Add to cart').' <i style="font-size: 100%" class="fa fa-cart-plus"></i>')),
                                            [
                                                'class' => 'add_to_cart-streamline ' . ($btn_class ? $btn_class : 'btn btn-danger btn-add_to_cart')
                                                    .' '.Local::choose([Local::RU => 'ru', Local::EU => 'eu']),
                                                'data-sku' => $v->id,
                                                'data-action' => 'add',
                                                'style' => 'width: 100%',
                                                'data-product' => Json::encode($data_product)
                                            ]
                                        );

                                        $complete = Html::tag('div', Yii::t('app', 'PRODUCT HAS BEEN ADDED TO CART'), [
                                            'class' => 'add-to-cart--complete'
                                        ]);

                                        $fail = Html::tag('div', Yii::t('app', 'ВЫ НЕ ВЫБРАЛИ ЛИНЗЫ!'), [
                                            'class' => 'add-to-cart--fail streamline'
                                        ]);

                                        echo $field . $btn . $complete . $fail;
                                        ?>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="col-md-7 col-md-pull-5 col-xs-12 information">
                            <h3><?= \Yii::t('app', 'Information') ?></h3>
                            <?= Yii::t('app', 'The improved hydrodynamic properties and low-profile design of Streamline goggles ensure maximum gliding efficiency in the water with minimum resistance. The goggles are fit with
a double strap and a low-profile seal that ensure their safe staying in place during racing. The lenses have a special anti-fog coating and UV protection. 3 interchangeable nose bridges
are enclosed. Mad Wave offers lenses with a wide dioptric range (from -1.0 to - 9.0).') ?>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-push-2">
            <div class="text-center" style="margin-bottom: 50px">
                <h2 style="font-size: 40px; font-weight: bold"><?= Yii::t('app', 'SEE THE CLEAREST, RACE THE FASTEST') ?></h2>
                <h4 style="color: #aaa"><?= Yii::t('app', 'Feel confidence like never before at starts with a selection of diopters from -1 to -9. Choose the optic strength of the
lens for each individual eye.') ?></h4>
            </div>
        </div>
    </div>
</div>
<section class="feature-new" style="position: relative;">
    <style>
        .add-to-cart--fail.streamline {
            display: none;
            text-align: center;
            margin-top: 15px;
            color: #aaa;
        }
        .blur {
            filter: blur(5px)
        }

        .case-non-blured {
            opacity: 1;
            font-weight: normal;
        }
        .blur + div .case-non-blured {
            opacity: .6 ;
            font-weight: bold;
        }

        .case-blured {
            opacity: .6 ;
            font-weight: bold;
        }
        .blur + div .case-blured {
            opacity: 1;
            font-weight: normal;
        }

        .case-blured, .case-non-blured {
            font-size: 36px;
            text-transform: uppercase;
            font-style: italic;
            display: block;
        }

        .blur-toggler {
            width: 56px;
            height: 32px;
            background: url('/images/new_features/streamline-toggler.png') center center / contain no-repeat;
            margin: 0 20px;
            padding: 3px;
            position: relative;
            top: 3px;
        }

        .toggle-button {
            display: block;
            width: 26px;
            height: 26px;
            background: url('/images/new_features/streamline-button.png') center center / contain no-repeat;
            float: right;
        }

        .blur + div .toggle-button {
            float: left;
        }


        .blur-toggler input {
            display: none;
        }

        .add_to_cart-streamline {
            transition: .3s
        }

        .add_to_cart-streamline.complete {
            background: #5bab5b;
            border: transparent;
        }

        .goggles {
            background: url('/images/new_features/goggles.png') left center / cover no-repeat;
            background-size: 200%;
            height: 450px;
            margin-top: -300px;
            margin-bottom: -50px;
        }

        @media (min-width: 760px) {
            .goggles {
                background: url('/images/new_features/goggles.png') center center / contain no-repeat;
                height: 350px;
                margin-bottom: 0px;
            }
            .case-blured, .case-non-blured {
                display: inline-block;
            }
        }

    </style>
    <div class="googles-water blur" style="background: url('/images/new_features/streamline_backgraound.jpg') center center / cover no-repeat;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: -1;"></div>
    <div class="container text-center">
        <div class="goggles">
        </div>

        <span class="case-blured"><?= Yii::t('app', 'NORMAL GOGGLES') ?></span>
        <label class="blur-toggler">
            <input type="checkbox" name="toggle-blur" checked>
            <span class="toggle-button"></span>
        </label>
        <span class="case-non-blured"><?= Yii::t('app', 'GOGGLES WITH DIOPTERS') ?></span>


        <?php $this->registerJs(<<<JS
            $('[name="toggle-blur"]').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.googles-water').addClass('blur');
                } else {
                    $('.googles-water').removeClass('blur');
                }
            });
            $('body').on('click', '.add_to_cart-streamline', function () {

                $('.add-to-cart--fail.streamline').hide();
                
                var btn = $(this),
                    quantity = btn.parents('#variant').find('select[name="quantity"]').val(),
                    quantity_field = $('#add_to_cart-quantity');
            
                if (!($('[name="left-lense"]').val().length > 0) || !($('[name="right-lense"]').val().length > 0)) {
                    $('.add-to-cart--fail.streamline').show();
                    return false;
                }
                
                if (btn.hasClass('btn-warning')) {
                    if (quantity_field.val().length < 6) {
                        quantity_field.addClass('hasError');
                        return false;
                    }
                } 
                
                if (btn.hasClass('complete')) {
                    return window.location = '/cart'
                }
                btn.addClass('complete');

                
                
                $.ajax({
                    type: "POST",
                    url:  '/ajax/add-to-cart',
                    data: {
                        sku: [$(this).data('sku'), $('[name="left-lense"]').val(), $('[name="right-lense"]').val()],
                        quantity: quantity,
                        subscriber: $(this).parents('.add-to-cart-container').find('input[name="subscriber"]').val()
                    },
                    beforeSend: function () {
                        btn.addClass('loading');
                    },
                    complete: function () {
                        btn.removeClass('loading');
                    },
                    success: function(response){
                        
                        refreshCart();
                        if (btn.hasClass('btn-warning')) {
                            btn.text('Оповещание придет на ваш e-mail');
                            return false;
                        }
                    },
                    error: function(response){
                        console.log(response);
                    }
                })
            })
JS
        ) ?>
    </div>
</section>

<div class="shadow catalog view">
    <div class="container">

        <div class="product-view row mb60">

            <?php

            /**
             * @var $dataProvider \yii\data\ActiveDataProvider
             * @var $v \common\models\ProductVariant
             */

            $relatedProductString = \Yii::t('app', 'Related products');
            ?>
            <div class="col-xs-12">
                <h2 class="text-center mb60" style="font-weight: bold; margin-top: 80px"><?= $relatedProductString ?></h2>
                <div class="row">
                    <div class="related-products">
                        <?php
                        $related = [
                            ProductVariant::findOne(10021309)
                        ];
                        foreach ($related as $v) {
                            echo $v->preview($relatedProductString, $i, $size);
                        } ?>
                    </div>
                </div>
            </div>

            <?= $product_reviews ?>

            <?= \common\widgets\product\page\ViewedProducts::widget() ?>
        </div>
    </div>
</div>