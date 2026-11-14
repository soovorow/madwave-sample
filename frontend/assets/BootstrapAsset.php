<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class BootstrapAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/bootstrap/bootstrap.js',
    ];
    public $css = [
//        That was moved to #deferred-styles
//        'css/style.min.css',
    ];
    public $jsOptions = [
        'async' => 'async',
    ];
    public $depends = [
//        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}