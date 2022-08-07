<?php
namespace backend\controllers\rbac;

use Yii;
use yii\filters\AccessControl;
use common\rbac\UserTypeRule;
use common\rbac\RoomOwnerRule;
use common\rbac\UpdateOwnRoomRule;
use common\rbac\HasRoomAccessRule;
use common\rbac\AskForAccessRule;
use common\rbac\MakeProposalRule;
use common\rbac\RoomPublishedRule;

class InitController extends \backend\controllers\Controller
{
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

    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        $userTypeRule = new UserTypeRule;
        $auth->add($userTypeRule);

        // add "updateRoom" permission
        $updateRoom = $auth->createPermission('updateRoom');
        $updateRoom->description = 'Update room';
        $auth->add($updateRoom);

        // add "updateOwnRoom" permission
        $ownerRule = new RoomOwnerRule;
        $auth->add($ownerRule);

        $updateOwnRoomRule = new UpdateOwnRoomRule;
        $auth->add($updateOwnRoomRule);

        $updateOwnRoom = $auth->createPermission('updateOwnRoom');
        $updateOwnRoom->description = 'Update own room';
        $updateOwnRoom->ruleName = $updateOwnRoomRule->name;
        $auth->add($updateOwnRoom);

        // "updateOwnRoom" will be used from "updateRoom"
        $auth->addChild($updateOwnRoom, $updateRoom);
         
        // add "askForAccess" permission
        $askForAccessRule = new AskForAccessRule;
        $auth->add($askForAccessRule);

        $askForAccess = $auth->createPermission('askForAccess');
        $askForAccess->description = 'Ask for room access';
        $askForAccess->ruleName = $askForAccessRule->name;
        $auth->add($askForAccess);

        // add "makeProposal" permission
        $makeProposalRule = new MakeProposalRule;
        $auth->add($makeProposalRule);

        $makeProposal = $auth->createPermission('makeProposal');
        $makeProposal->description = 'Make proposal';
        $makeProposal->ruleName = $makeProposalRule->name;
        $auth->add($makeProposal);
        
        // add "seeRoomDetails" permission
        $hasRoomAccessRule = new HasRoomAccessRule;
        $auth->add($hasRoomAccessRule);

        $seeRoomDetails = $auth->createPermission('seeRoomDetails');
        $seeRoomDetails->description = 'See room details';
        $auth->add($seeRoomDetails);

        $seeOwnRoomDetails = $auth->createPermission('seeOwnRoomDetails');
        $seeOwnRoomDetails->description = 'See own room details';
        $seeOwnRoomDetails->ruleName = $ownerRule->name;
        $auth->add($seeOwnRoomDetails);

        $seeAllowedRoomDetails = $auth->createPermission('seeAllowedRoomDetails');
        $seeAllowedRoomDetails->description = 'See allowed room details';
        $seeAllowedRoomDetails->ruleName = $hasRoomAccessRule->name;
        $auth->add($seeAllowedRoomDetails);

        $auth->addChild($seeOwnRoomDetails, $seeRoomDetails);
        $auth->addChild($seeAllowedRoomDetails, $seeRoomDetails);

        // add "viewRoom" permission
        $roomPublishedRule = new RoomPublishedRule;
        $auth->add($roomPublishedRule);

        $viewRoom = $auth->createPermission('viewRoom');
        $viewRoom->description = 'View room';
        $auth->add($viewRoom);

        $viewOwnRoom = $auth->createPermission('viewOwnRoom');
        $viewOwnRoom->description = 'View own room';
        $viewOwnRoom->ruleName = $ownerRule->name;
        $auth->add($viewOwnRoom);

        $viewPublishedRoom = $auth->createPermission('viewPublishedRoom');
        $viewPublishedRoom->description = 'View published room';
        $viewPublishedRoom->ruleName = $roomPublishedRule->name;
        $auth->add($viewPublishedRoom);

        $auth->addChild($viewOwnRoom, $viewRoom);
        $auth->addChild($viewPublishedRoom, $viewRoom);

        // Create anonymous role
        $anon = $auth->createRole('anonymous');
        $anon->ruleName = $userTypeRule->name;
        $auth->add($anon);
        $auth->addChild($anon, $viewPublishedRoom);
        $auth->addChild($anon, $askForAccess);

        // Create user role
        $user = $auth->createRole('user');
        $user->ruleName = $userTypeRule->name;
        $auth->add($user);
        $auth->addChild($user, $viewPublishedRoom);
        $auth->addChild($user, $askForAccess);
        $auth->addChild($user, $makeProposal);
        $auth->addChild($user, $seeAllowedRoomDetails);

        // Create manager role
        $manager = $auth->createRole('manager');
        $manager->ruleName = $userTypeRule->name;
        $auth->add($manager);
        $auth->addChild($manager, $viewPublishedRoom);
        $auth->addChild($manager, $viewOwnRoom);
        $auth->addChild($manager, $updateOwnRoom);
        $auth->addChild($manager, $seeOwnRoomDetails);

        // Create admin role
        $admin = $auth->createRole('admin');
        $admin->ruleName = $userTypeRule->name;
        $auth->add($admin);
        $auth->addChild($admin, $viewRoom);
        $auth->addChild($admin, $updateRoom);
        $auth->addChild($admin, $seeRoomDetails);

        // Create superadmin role
        $superadmin = $auth->createRole('superadmin');
        $superadmin->ruleName = $userTypeRule->name;
        $auth->add($superadmin);
        $auth->addChild($superadmin, $admin);

        Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Auth rules were re-generated.'));
        return $this->redirect('/rbac');
    }
}
