<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Petr Dvukhrechesky <petr.sdkb@gmail.com>
 */
class CanjsAsset extends AssetBundle
{
    public $sourcePath = '@frontend/web/js/canjs-jquery';

    public $css = [

    ];

    public $js = [
        'can.jquery.js',
    ];

    public $depends = [
        'yii\web\YiiAsset'
    ];
}
