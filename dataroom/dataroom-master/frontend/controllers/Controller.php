<?php

namespace frontend\controllers;

use common\models\User;
use Yii;
use yii\web\Controller as BaseFrontendController;

class Controller extends BaseFrontendController
{
    public $showContactForm = false;

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {

            if (($action->controller->id == 'job-offer' && $action->id == 'index')
                || ($action->controller->id == 'news' && $action->id == 'category')
                || ($action->controller->id == 'site' && $action->id == 'trendy-page' && Yii::$app->request->get('id') == 8) // missions-de-reference
                || ($action->controller->id == 'site' && $action->id == 'trendy-page' && Yii::$app->request->get('id') == 19) // classements
            ) {
                $this->showContactForm = true;
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function goBack($defaultUrl = null)
    {
        $this->setHomeUrl();

        return parent::goBack($defaultUrl);
    }

    /**
     * @inheritdoc
     */
    public function goHome()
    {
        $this->setHomeUrl();

        return parent::goHome();
    }

    /**
     * Set home url
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    protected function setHomeUrl()
    {
        if (!Yii::$app->user->isGuest) {
            switch (Yii::$app->user->identity->type) {
                case User::TYPE_USER:
                case User::TYPE_MANAGER:
                    Yii::$app->setHomeUrl(['/dataroom/user/my-rooms']);
                    break;

                default:
                    Yii::$app->setHomeUrl('/');
            }
        }
    }
}