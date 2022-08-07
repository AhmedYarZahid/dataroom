<?php
namespace backend\controllers\rbac;

use Yii;
use mdm\admin\controllers\AssignmentController as BaseAssignmentController;
use yii\filters\AccessControl;

/**
 * Assignment controller
 */
class AssignmentController extends BaseAssignmentController
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
        $this->title = Yii::t('admin', 'Assignments');
        $this->titleSmall = Yii::t('admin', 'Manage user assignments');

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
