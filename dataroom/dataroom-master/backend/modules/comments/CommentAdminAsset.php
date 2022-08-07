<?php

namespace backend\modules\comments;

use yii\web\AssetBundle;

/**
 * @author Petr Dvukhrechesky <petr.sdkb@gmail.com>
 */
class CommentAdminAsset extends AssetBundle
{
    public $css = [
        'css/comment-admin.less',
    ];

    public $js = [
        'js/comment-admin.js',
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
