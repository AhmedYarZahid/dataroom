<?php

namespace backend\modules\trendypage;

use lateos\trendypage\Module as BaseModule;

class Module extends BaseModule
{
    //public $controllerNamespace = 'app\modules\trendypage\controllers';
    
    public $controllerMap = [
        'manage' => 'backend\modules\trendypage\controllers\ManageController',
    ];

    public function init()
    {
        parent::init();

        $this->setViewPath('@vendor/lateos/yii2-trendy-page/views');
    }
}
