<?php

namespace backend\modules\dataroom;

use backend\modules\dataroom\models\AbstractProfile;
use backend\modules\dataroom\models\RoomAccessRequestCoownership;
use backend\modules\dataroom\models\RoomAccessRequestCV;
use backend\modules\dataroom\models\RoomAccessRequestRealEstate;
use Exception;
use Yii;
use backend\modules\dataroom\Module as DataroomModule;
use backend\modules\dataroom\models\AbstractDetailedRoom;
use backend\modules\dataroom\models\AbstractAccessRequest;
use backend\modules\dataroom\models\RoomAccessRequest;
use backend\modules\dataroom\models\RoomAccessRequestCompany;
use common\models\User;

class AccessRequestManager
{
    protected $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Creates a new access request and a user profile if needed.
     *
     * @param AbstractDetailedRoom $room
     * @param AbstractAccessRequest $accessRequest
     * @param User $user
     * @param AbstractProfile $profile
     *
     * @return bool Whether the request was created.
     *
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function createAccessRequest(AbstractDetailedRoom $room, AbstractAccessRequest $accessRequest, User $user, AbstractProfile $profile)
    {
        $valid = $accessRequest->validate();
        $user->scenario = 'get-room-access';

        if (!$user->id) {
            $user->type = $user::TYPE_USER;
            $valid = $user->validate() && $valid;
        }

        if ($user->isMailingContact) {
            $valid = $profile->validate() && $valid;
        } else {
            // Set null for all fields (user may fill data later via "My Profile" page)
            foreach ($profile::getTableSchema()->columns as $column) {
                $profile->{$column->name} = null;
            }
        }

        if ($valid) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $section = $this->getRequestSection($accessRequest);
                
                $this->userManager->save($user, $section, true);

                $baseAccessRequest = new RoomAccessRequest([
                    'roomID' => $room->roomID,
                    'userID' => $user->id,
                ]);
                $baseAccessRequest->save(false);

                $accessRequest->accessRequestID = $baseAccessRequest->id;
                $accessRequest->save(false);

                // Save profile
                $profile->userID = $user->id;
                $profile->save(false);

                $transaction->commit();

                return true;
            } catch (Exception $e) {
                $transaction->rollBack();

                Yii::error($e->__toString(), DataroomModule::LOG_CATEGORY);
                throw $e;
            }
        }

        return false;
    }

    /**
     * Returns section related to a given request.
     * 
     * @param  AbstractAccessRequest $accessRequest
     * @return string Section name.
     */
    protected function getRequestSection(AbstractAccessRequest $accessRequest)
    {
        switch (get_class($accessRequest)) {
            case RoomAccessRequestCompany::class:
                return DataroomModule::SECTION_COMPANIES;

            case RoomAccessRequestRealEstate::class:
                return DataroomModule::SECTION_REAL_ESTATE;

            case RoomAccessRequestCoownership::class:
                return DataroomModule::SECTION_COOWNERSHIP;

            case RoomAccessRequestCV::class:
                return DataroomModule::SECTION_CV;
            
            default:
                return null;
        }
    }
}