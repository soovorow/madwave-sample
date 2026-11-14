<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/slick/slick.min.js',
        'js/theia-sticky-sidebar.min.js',
        'js/bootstrap-media-lightbox.js',

        'js/nouislider/nouislider.min.js',
        'js/jquery.elevateZoom.min.js',
        'js/product_view.js',
        'js/city-detector.js',

        'js/bootstrap/bootstrap.js',

        'js/checkout.js',
        'js/cart__check.js',
        'js/checkout/methodCourierInit.js',
        'js/checkout/methodMailInit.js',
        'js/checkout/methodPickupInit.js',

        'js/custom.js',
    ];
    public $css = [
//        That was moved to #deferred-styles
//        'css/style.min.css',
    ];
    public $jsOptions = [
        'async' => 'async',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}