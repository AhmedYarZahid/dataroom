<?php
namespace backend\controllers\rbac;

use Yii;
use mdm\admin\controllers\RouteController as BaseRouteController;
use yii\filters\AccessControl;

/**
 * Route controller
 */
class RouteController extends BaseRouteController
{
    /**
     * @var string Title of controller
     */
    public $title;

    /**
     * @var string Small title of conreoller
     */
    public $titleSmall;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'Routes');
        $this->titleSmall = Yii::t('admin', 'Manage routes');

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
}
