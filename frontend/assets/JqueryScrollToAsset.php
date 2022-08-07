<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 */
class JqueryScrollToAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];

    public $js = [
        'js/jquery-scrollto/jquery.scrollTo.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}