<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Petr Dvukhrechesky <petr.sdkb@gmail.com>
 */
class TrendyPageProjectStylesAsset extends AssetBundle
{
    public $sourcePath = '@frontend/web/less';

    public $css = [
        'trendy-page.less',
    ];

    public $js = [

    ];
}
