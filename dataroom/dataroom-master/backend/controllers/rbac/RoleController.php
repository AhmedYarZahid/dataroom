<?php
namespace backend\controllers\rbac;

use Yii;
use mdm\admin\controllers\RoleController as BaseRoleController;
use yii\filters\AccessControl;

/**
 * Role controller
 */
class RoleController extends BaseRoleController
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
        $this->title = Yii::t('admin', 'Roles');
        $this->titleSmall = Yii::t('admin', 'Manage roles');

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
