<?php

namespace frontend\widgets\cookiebar;

use yii\web\AssetBundle;

/**
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 */
class CookieBarAsset extends AssetBundle
{
    public $css = [
        'css/cookiebar.css',
    ];

    public $js = [
        'js/jquery.cookiebar.js'
    ];

    public $depends = [
        'yii\web\YiiAsset'
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
