<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        [
            'href' => 'images/aja-icon-32x32.png',
            'rel' => 'icon',
            'sizes' => '32x32',
        ],
        [
            'href' => 'images/aja-icon-192x192.png',
            'rel' => 'icon',
            'sizes' => '192x192',
        ],
        [
            'href' => 'images/aja-icon-180x180.png',
            'rel' => 'apple-touch-icon-precomposed',
        ],
        'css/main.css',
    ];
    public $js = [
        'vendor/tweenmax/TweenMax.min.js',
        'js/site.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'frontend\assets\JqueryMarqueeAsset',
    ];
}
