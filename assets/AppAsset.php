<?php

namespace cms\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/app';

    public $css = [
        'site.css',
        'controls.css',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
