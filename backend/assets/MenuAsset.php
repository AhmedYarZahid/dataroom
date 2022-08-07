<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Petr Dvukhrechensky <petr.sdkb@gmail.com>
 */
class MenuAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/menu-editor.less',
    ];

    public $js = [
        'vendor/handlebars/handlebars-v4.0.5.js',
        'vendor/nested-sortable/jquery.mjs.nestedSortable.js',
        'js/menu-editor.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'frontend\assets\JqueryUiAsset',
    ];
}
