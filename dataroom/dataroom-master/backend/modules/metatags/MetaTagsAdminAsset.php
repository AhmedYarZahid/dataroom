<?php

namespace backend\modules\metatags;

use yii\web\AssetBundle;

/**
 * @author Petr Dvukhrechesky <petr.sdkb@gmail.com>
 */
class MetaTagsAdminAsset extends AssetBundle
{
    public $css = [
        'css/meta-tags-admin.less',
    ];

    public $js = [
        'js/meta-tags-admin.js',
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
