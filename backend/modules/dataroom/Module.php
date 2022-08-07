<?php

namespace backend\modules\dataroom;

use Yii;

class Module extends \yii\base\Module
{
    const LOG_CATEGORY = 'dataroom';

    const SECTION_COMPANIES = 'companies';
    const SECTION_REAL_ESTATE = 'real_estate';
    const SECTION_COOWNERSHIP = 'coownership';
    const SECTION_CV = 'cv';

    public $controllerNamespace = 'backend\modules\dataroom\controllers';
    
    public $defaultRoute = 'companies/room/index';

    public function init()
    {
        parent::init();
    }
}
