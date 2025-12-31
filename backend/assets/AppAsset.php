<?php

namespace backend\assets;

use rmrevin\yii\fontawesome\CdnFreeAssetBundle as FontAwesomeAsset;
use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $css = ['css/site.css'];

    public $js = [];

    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
        FontAwesomeAsset::class,
    ];

    public function init()
    {
        parent::init();
        $this->sourcePath = dirname(__FILE__) . '/files';
    }

}
