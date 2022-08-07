<?php

namespace backend\modules\trendypage;

use yii\web\AssetBundle;

class LayoutBuilderAdditionalObjectsAsset extends AssetBundle
{
    public $css = [
        'less/layout-builder.less',
    ];

    public $js = [
        'js/layout-builder.js',
    ];

    public $depends = [
        'frontend\assets\TrendyPageProjectStylesAsset',
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
