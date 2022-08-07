<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Petr Dvukhrechesky <petr.sdkb@gmail.com>
 */
class SmartPhotoAsset extends AssetBundle
{
    public $sourcePath = '@frontend/web/vendor/smartphoto';

    public $css = [
        'smartphoto.min.css',
    ];

    public $js = [
        'smartphoto.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}