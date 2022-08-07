<?php

namespace frontend\components;

use lateos\trendypage\models\TrendyPage;
use yii\base\BaseObject;
use yii\web\UrlRuleInterface;

class TrendyPageUrlRule extends BaseObject implements UrlRuleInterface
{

    public function createUrl($manager, $route, $params)
    {
        if ($route === 'site/trendy-page') {
            if (isset($params['id'])) {
                if ($trendyPage = TrendyPage::getPage($params['id'])) {
                    return $trendyPage->slug;
                }
            }
        }

        return false;  // this rule does not apply
    }

    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();

        if (preg_match('%^([0-9a-zA-Z\-]+)$%', $pathInfo, $matches)) {
            if ($trendyPageLang = TrendyPage::getPageBySlug($matches[1])) {
                return ['site/trendy-page', ['id' => $trendyPageLang->trendyPageID]];
            }
        }

        return false;  // this rule does not apply
    }
}