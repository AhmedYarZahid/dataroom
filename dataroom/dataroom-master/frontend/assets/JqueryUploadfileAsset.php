<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Petr Dvukhrechesky <petr.sdkb@gmail.com>
 */
class JqueryUploadfileAsset extends AssetBundle
{
    public $sourcePath = '@frontend/web/js/jquery-uploadfile';

    public $css = [
        'uploadfile.css'
    ];

    public $js = [
        'jquery.uploadfile.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset'
    ];
}
