<?php

namespace backend\modules\office;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $css = [
        'css/office-admin.less',
    ];

    public $js = [
        'js/vendor/jquery-ui.min.js',
        'js/vendor/vue.js',
        'js/office-admin.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];

    public $publishOptions = [
        'forceCopy' => true,
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
