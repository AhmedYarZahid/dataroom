<?php
namespace backend\controllers\rbac;

use Yii;
use mdm\admin\controllers\PermissionController as BasePermissionController;
use yii\filters\AccessControl;

/**
 * Permission controller
 */
class PermissionController extends BasePermissionController
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
        $this->title = Yii::t('admin', 'Permissions');
        $this->titleSmall = Yii::t('admin', 'Manage permissions');

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
