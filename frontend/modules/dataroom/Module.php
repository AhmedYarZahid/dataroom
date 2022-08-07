<?php

namespace frontend\modules\dataroom;

use Yii;

class Module extends \yii\base\Module
{
    public $layout = 'main';

    public $controllerNamespace = 'frontend\modules\dataroom\controllers';
    
    public $defaultRoute = 'companies';

    public function init()
    {
        parent::init();

        Yii::$app->errorHandler->errorAction = 'dataroom/default/error';
    }
}
