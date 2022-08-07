<?php

namespace backend\assets;

use yii\web\AssetBundle;

class MultiSelectAsset extends AssetBundle
{
    public $basePAth = "@web";
//    public $sourcePath = '@bower';

    public $css = [
        'admin/css/multi-select.dist.css',
    ];

    public $js = [
        'admin/js/jquery.multi-select.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
