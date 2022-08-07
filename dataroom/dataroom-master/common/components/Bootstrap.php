<?php

namespace common\components;

use common\helpers\ArrayHelper;
use yii\base\BootstrapInterface;
use \common\models\Language;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $defaultLanguage = Language::getDefaultLanguage();

        $app->params['languagesList'] = Language::getList();
        $app->params['defaultLanguage'] = $defaultLanguage;
        $app->params['defaultLanguageID'] = $defaultLanguage->id;

        $languagesList = ArrayHelper::map($app->params['languagesList'], 'id', 'id');

        $app->urlManager->languages = $languagesList;
        $app->urlManagerFrontend->languages = $languagesList;
        $app->urlManagerBackend->languages = $languagesList;
    }
}