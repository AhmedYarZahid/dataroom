<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Petr Dvukhrechesky <petr.sdkb@gmail.com>
 */
class JqueryUiAsset extends AssetBundle
{
    public $sourcePath = '@frontend/web/js/jquery-ui';

    public $css = [
        'jquery-ui.min.css',
    ];

    public $js = [
        'jquery-ui.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}