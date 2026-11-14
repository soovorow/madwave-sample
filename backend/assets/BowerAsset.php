<?php namespace backend\assets;

use yii\web\AssetBundle;

class BowerAsset extends AssetBundle {
    public $sourcePath = '@bower';
    public $css = [

    ];
    public $js = [
        'theia-sticky-sidebar/js/theia-sticky-sidebar.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
