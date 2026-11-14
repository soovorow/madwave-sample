<?php
/**
 * Configuration file for the "yii asset" console command.
 */

// In the console environment, some path aliases may not exist. Please define these:
 Yii::setAlias('@webroot', __DIR__ . '/../web');
 Yii::setAlias('@web', '/');

return [
    // Adjust command/callback for JavaScript files compressing:
    'jsCompressor' => 'java -jar '.Yii::getAlias('@extras').'/compiler/compiler.jar --js {from} --js_output_file {to}',
    // Adjust command/callback for CSS files compressing:
    'cssCompressor' => 'java -jar '.Yii::getAlias('@extras').'/yuicompressor/yuicompressor-2.4.8.jar --type css {from} -o {to}',
    // Whether to delete asset source after compression:
    'deleteSource' => false,
    // The list of asset bundles to compress:

    'bundles' => [
        'frontend\assets\BootstrapAsset' ,
        'frontend\assets\AppAsset' ,
        'yii\widgets\ActiveFormAsset',
        'yii\validators\ValidationAsset',
        'yii\authclient\widgets\AuthChoiceAsset'
    ],
    // Asset bundle for compression output:
    'targets' => [
        'all' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'js' => 'js/all-{hash}.js',
//            'css' => 'css/all-{hash}.css',
//            'jsOptions' => [
//                'async' => 'async'
//            ],
        ],
    ],
    // Asset manager configuration:
    'assetManager' => [
        'basePath' => '@webroot/assets',
        'baseUrl' => '@web/assets',
    ],
];