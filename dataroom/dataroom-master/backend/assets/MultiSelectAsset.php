<?php

namespace backend\assets;

use yii\web\AssetBundle;

class MultiSelectAsset extends AssetBundle
{
    public $sourcePath = '@bower';

    public $css = [
        'multiselect/css/multi-select.dist.css',
    ];

    public $js = [
        'multiselect/js/jquery.multi-select.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
