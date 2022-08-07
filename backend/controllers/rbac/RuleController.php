<?php
namespace backend\controllers\rbac;

use Yii;
use mdm\admin\controllers\RuleController as BaseRuleController;
use yii\filters\AccessControl;

/**
 * Rule controller
 */
class RuleController extends BaseRuleController
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
        $this->title = Yii::t('admin', 'Rules');
        $this->titleSmall = Yii::t('admin', 'Manage rules');

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
