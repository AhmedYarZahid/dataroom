<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class JqueryMarqueeAsset extends AssetBundle
{
    //public $basePath = '@webroot';
    //public $baseUrl = '@web';
    public $sourcePath = '@bower';

    public $js = [
        'jquery.marquee/jquery.marquee.min.js',
        //'js/jquery.ticker.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
