<?php

namespace backend\modules\mailing;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'backend\modules\mailing\controllers';
    
    public $defaultRoute = 'list/index';

    public function init()
    {
        parent::init();
    }
}
