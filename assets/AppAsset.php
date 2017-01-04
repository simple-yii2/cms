<?php

namespace cms\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{

    public $css = [
        'site.css',
        'controls.css',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'kartik\dropdown\DropdownXAsset',
    ];

    public function init()
    {
    	$this->sourcePath = __DIR__ . '/app';
    }

}
