<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Petr Dvukhrechesky <petr.sdkb@gmail.com>
 */
class JqueryStepsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'js/jquery-steps/jquery.steps.css',
    ];

    public $js = [
        'js/jquery-steps/jquery.steps.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}