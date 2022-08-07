<?php

namespace common\widgets\isloading;

use yii\web\AssetBundle;

class IsLoadingAsset extends AssetBundle
{

    public $js = [
        'js/jquery.isloading.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';

        parent::init();
    }

}