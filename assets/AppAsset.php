<?php

namespace cms\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{

    public $css = [
        'site.css',
        'controls.css',
    ];

    public $js = [
        'site.js',
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
