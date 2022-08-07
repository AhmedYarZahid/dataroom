<?php

namespace backend\modules\comments\widgets\Comments;

use yii\web\AssetBundle;

/**
 * @author Petr Dvukhrechesky <petr.sdkb@gmail.com>
 */
class CommentsAsset extends AssetBundle
{
    public $css = [
        'css/comments.less',
    ];

    public $js = [
        //'js/comment-form.js',
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
