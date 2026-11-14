<?php

namespace frontend\widgets\easyfeatures\assets;

use yii\web\AssetBundle;

/**
 * Class EasyFeaturesAsset is asset bundle for EasyFeatures widget
 *
 * @package frontend\widgets\easyfeatures\assets
 *
 * @author Vyacheslav Elchin <lexscite@gmail.com>
 */
class EasyFeaturesAsset extends AssetBundle
{
    public $sourcePath = '@frontend/widgets/easyfeatures';
    public $css = [
        'css/easyfeatures.css',
    ];
    public $js = [
        'js/easyfeatures.js',
    ];
    public $jsOptions = [
        'async' => 'async',
    ];
    public $publishOptions = [
        'forceCopy' => YII_DEBUG,
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}