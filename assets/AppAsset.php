<?php

namespace cms\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{

    public $css = [
        'site' . (YII_DEBUG ? '' : '.min') . '.css',
        'controls' . (YII_DEBUG ? '' : '.min') . '.css',
    ];

    public $js = [
        'site' . (YII_DEBUG ? '' : '.min') . '.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/app';
    }

}
