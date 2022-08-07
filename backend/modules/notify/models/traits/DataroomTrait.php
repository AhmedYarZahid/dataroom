<?php

namespace backend\modules\notify\models\traits;

use backend\modules\dataroom\models\RoomCV;
use backend\modules\document\models\Document;
use Yii;
use yii\helpers\Html;
use common\models\User;
use backend\modules\dataroom\models\AbstractAccessRequest;
use backend\modules\dataroom\models\AbstractDetailedRoom;
use backend\modules\dataroom\models\AbstractProposal;
use backend\modules\dataroom\models\RoomCompany;

trait DataroomTrait
{
    public static function sendNewAccessRequest(AbstractAccessRequest $model)
    {
        $room = $model->accessRequest->room;
        $detailedRoom = $room->detailedRoom;
        $user = $model->accessRequest->user;

        $tags = [
            '{EMAIL}' => Html::encode($user->email),
            '{FIRST_NAME}' => Html::encode($user->firstName),
            '{LAST_NAME}' => Html::encode($user->lastName),
            '{ROOM_ID}' => $detailedRoom->id,
            '{ROOM_TITLE}' => Html::encode($room->title),
            '{ROOM_LINK}' => $detailedRoom->getUrlBackend(),
            '{REQUEST_ID}' => $model->accessRequestID,
            '{REQUEST_LINK}' => "https://dataroom.huda2598.odns.fr/admin/dataroom/companies/access-request/index?RoomAccessRequestCompanySearch[accessRequestID]=" . $model->accessRequestID,
        ];

        return self::sendNotifyToUser($room->admin, self::EVENT_NEW_ACCESS_REQUEST, $tags);

        //return self::sendNotifyToAja(self::EVENT_NEW_ACCESS_REQUEST, $tags);
    }

    public static function sendAccessRequestValidated(AbstractAccessRequest $model)
    {
        $room = $model->accessRequest->room;
        $detailedRoom = $room->detailedRoom;
        $user = $model->accessRequest->user;

        $tags = [
            '{EMAIL}' => Html::encode($user->email),
            '{FIRST_NAME}' => Html::encode($user->firstName),
            '{LAST_NAME}' => Html::encode($user->lastName),
            '{ROOM_ID}' => $detailedRoom->id,
            '{ROOM_TITLE}' => Html::encode($room->title),
            '{ROOM_LINK}' => $detailedRoom->getUrl(),
        ];

        return self::sendNotifyToUser($user, self::EVENT_ACCESS_REQUEST_VALIDATED, $tags);
    }

    /**
     * Send "Access request refused" notify
     *
     * @param AbstractAccessRequest $model
     * @return bool
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     */
    public static function sendAccessRequestRefused(AbstractAccessRequest $model)
    {
        $room = $model->accessRequest->room;
        $detailedRoom = $room->detailedRoom;
        $user = $model->accessRequest->user;

        $tags = [
            '{EMAIL}' => Html::encode($user->email),
            '{FIRST_NAME}' => Html::encode($user->firstName),
            '{LAST_NAME}' => Html::encode($user->lastName),
            '{ROOM_ID}' => $detailedRoom->id,
            '{ROOM_TITLE}' => Html::encode($room->title),
            '{ROOM_LINK}' => $detailedRoom->getUrl(),
        ];

        return self::sendNotifyToUser($user, self::EVENT_ACCESS_REQUEST_REFUSED, $tags);
    }

    public static function sendRoomCreated(AbstractDetailedRoom $model)
    {
        $user = $model->room->user;

        $tags = [
            '{EMAIL}' => Html::encode($user->email),
            '{FIRST_NAME}' => Html::encode($user->firstName),
            '{LAST_NAME}' => Html::encode($user->lastName),
            '{ROOM_ID}' => $model->id,
            '{ROOM_TITLE}' => Html::encode($model->room->title),
            '{ROOM_LINK}' => $user->isAdmin() ? $model->getUrlBackend() : $model->getUrl(),
        ];

        return self::sendNotifyToUser($user, self::EVENT_NEW_ROOM_CREATED, $tags);
    }

    public static function sendManagerCreated($user)
    {
        $tags = [
            '{EMAIL}' => Html::encode($user->email),
            '{FIRST_NAME}' => Html::encode($user->firstName),
            '{LAST_NAME}' => Html::encode($user->lastName),
            '{ONE_TIME_LOGIN_LINK}' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['one-time-login', 'token' => $user->oneTimeLoginToken]),
        ];

        return self::sendNotifyToUser($user, self::EVENT_MANAGER_REGISTRATION, $tags);
    }

    public static function sendNewProposalToBuyer(AbstractProposal $model)
    {
        $user = $model->proposal->user;
        $detailedRoom = $model->proposal->room->detailedRoom;

        $tags = [
            '{EMAIL}' => Html::encode($user->email),
            '{FIRST_NAME}' => Html::encode($user->firstName),
            '{LAST_NAME}' => Html::encode($user->lastName),
            '{ROOM_ID}' => $detailedRoom->id,
            '{ROOM_TITLE}' => Html::encode($model->proposal->room->title),
            '{ROOM_LINK}' => $detailedRoom->getUrl(),
        ];

        return self::sendNotifyToUser($user, self::EVENT_NEW_PROPOSAL, $tags);
    }

    public static function sendNewProposalToAdmin(AbstractProposal $model)
    {
        $user = $model->proposal->user;
        $detailedRoom = $model->proposal->room->detailedRoom;

        $tags = [
            '{EMAIL}' => Html::encode($user->email),
            '{FIRST_NAME}' => Html::encode($user->firstName),
            '{LAST_NAME}' => Html::encode($user->lastName),
            '{ROOM_ID}' => $detailedRoom->id,
            '{ROOM_TITLE}' => Html::encode($model->proposal->room->title),
            '{ROOM_LINK}' => $detailedRoom->getUrlBackend(),
            '{PROPOSAL_ID}' => $model->proposal->id,
            '{PROPOSAL_LINK}' => $model->getUrl(),
        ];

        return self::sendNotifyToUser($model->proposal->room->admin, self::EVENT_NEW_PROPOSAL_TO_AJA, $tags);

        //return self::sendNotifyToAja(self::EVENT_NEW_PROPOSAL_TO_AJA, $tags);
    }

    public static function sendRoomPublishingSoon(AbstractDetailedRoom $model)
    {
        $user = $model->room->user;

        $tags = [
            '{EMAIL}' => Html::encode($user->email),
            '{FIRST_NAME}' => Html::encode($user->firstName),
            '{LAST_NAME}' => Html::encode($user->lastName),
            '{ROOM_ID}' => $model->id,
            '{ROOM_TITLE}' => Html::encode($model->room->title),
            '{ROOM_LINK}' => $model->getUrlBackend(),
        ];

        return self::sendNotifyToUser($model->room->admin, self::EVENT_ROOM_PUBLICATION, $tags);

        //return self::sendNotifyToAja(self::EVENT_ROOM_PUBLICATION, $tags);
    }

    public static function sendRoomExpiringSoon(AbstractDetailedRoom $model)
    {
        $user = $model->room->user;

        $tags = [
            '{EMAIL}' => Html::encode($user->email),
            '{FIRST_NAME}' => Html::encode($user->firstName),
            '{LAST_NAME}' => Html::encode($user->lastName),
            '{ROOM_ID}' => $model->id,
            '{ROOM_TITLE}' => Html::encode($model->room->title),
            '{ROOM_LINK}' => $model->getUrlBackend(),
        ];

        return self::sendNotifyToUser($model->room->admin, self::EVENT_ROOM_EXPIRATION, $tags);

        //return self::sendNotifyToAja(self::EVENT_ROOM_EXPIRATION, $tags);
    }

    public static function sendHearingSoon(RoomCompany $model)
    {
        $tags = [
            '{ROOM_ID}' => $model->id,
            '{ROOM_TITLE}' => Html::encode($model->room->title),
            '{ROOM_LINK}' => $model->getUrl(),
        ];

        $sent = true;

        $buyers = $model->room->getBuyers();
        foreach ($buyers as $user) {
            $sent = self::sendNotifyToUser($user, self::EVENT_ROOM_HEARING, $tags) && $sent;
        }

        return $sent;
    }

    public static function sendRoomExpired(AbstractDetailedRoom $model)
    {
        $user = $model->room->user;

        $tags = [
            '{EMAIL}' => Html::encode($user->email),
            '{FIRST_NAME}' => Html::encode($user->firstName),
            '{LAST_NAME}' => Html::encode($user->lastName),
            '{ROOM_ID}' => $model->id,
            '{ROOM_TITLE}' => Html::encode($model->room->title),
            '{ROOM_LINK}' => $model->getUrlBackend(),
        ];

        return self::sendNotifyToUser($model->room->admin, self::EVENT_ROOM_EXPIRED, $tags);

        //return self::sendNotifyToAja(self::EVENT_ROOM_EXPIRED, $tags);
    }

    public static function sendRoomArchived(AbstractDetailedRoom $model)
    {
        $user = $model->room->user;

        $tags = [
            '{EMAIL}' => Html::encode($user->email),
            '{FIRST_NAME}' => Html::encode($user->firstName),
            '{LAST_NAME}' => Html::encode($user->lastName),
            '{ROOM_ID}' => $model->id,
            '{ROOM_TITLE}' => Html::encode($model->room->title),
            '{ROOM_LINK}' => $model->getUrlBackend(),
        ];

        return self::sendNotifyToUser($model->room->admin, self::EVENT_ROOM_ARCHIVED, $tags);

        //return self::sendNotifyToAja(self::EVENT_ROOM_ARCHIVED, $tags);
    }

    public static function sendRoomUpdatedToAja(AbstractDetailedRoom $model)
    {
        $tags = [
            '{ROOM_ID}' => $model->id,
            '{ROOM_TITLE}' => Html::encode($model->room->title),
            '{ROOM_LINK}' => $model->getUrlBackend(),
        ];

        return self::sendNotifyToUser($model->room->admin, self::EVENT_ROOM_UDPATED_TO_AJA, $tags);

        //return self::sendNotifyToAja(self::EVENT_ROOM_UDPATED_TO_AJA, $tags);
    }

    public static function sendRoomUpdatedToBuyers(AbstractDetailedRoom $model)
    {
        $tags = [
            '{ROOM_ID}' => $model->id,
            '{ROOM_TITLE}' => Html::encode($model->room->title),
            '{ROOM_LINK}' => $model->getUrl(),
        ];

        $sent = true;

        $buyers = $model->room->getBuyers();
        foreach ($buyers as $user) {
            $sent = self::sendNotifyToUser($user, self::EVENT_ROOM_UPDATED_TO_BUYERS, $tags) && $sent;
        }

        return $sent;
    }

    /**
     * Send notify to room creator about newly uploaded CV
     *
     * @param RoomCV $model
     * @return boolean
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     */
    public static function sendRoomCVUploaded(RoomCV $model)
    {
        $creator = $model->room->creator;

        $tags = [
            '{ROOM_ID}' => $model->id,
            '{ROOM_TITLE}' => Html::encode($model->room->title),
            '{ROOM_LINK}' => $model->getUrlBackend(),
        ];

        return self::sendNotifyToUser($creator, self::EVENT_ROOM_CV_UPLOADED, $tags);
    }

    /**
     * Send notify to room manager that need to correct CV
     *
     * @param RoomCV $model
     * @return boolean
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     */
    public static function sendRoomCVToCorrect(RoomCV $model)
    {
        $manager = $model->room->user;

        $tags = [
            '{ROOM_ID}' => $model->id,
            '{ROOM_TITLE}' => Html::encode($model->room->title),
            '{ROOM_LINK}' => $manager->isAdmin() ? $model->getUrlBackend() : $model->getUrl(),
        ];

        return self::sendNotifyToUser($manager, self::EVENT_ROOM_CV_NEED_TO_CORRECT, $tags);
    }

    /**
     * Send "New document(s) was added to the room" notify
     *
     * @param Document[] $documentModels
     * @return mixed
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     */
    public static function sendDocumentsUploadedToRoom($documentModels)
    {
        $documentModels[0]->refresh();
        $roomModel = $documentModels[0]->room;

        $documentsList = [];
        foreach ($documentModels as $documentModel) {
            $documentsList[] = '- ' . $documentModel->title;
        }

        $tags = [
            '{DOCUMENTS_LIST}' => join("<br>", $documentsList),
            '{ROOM_NAME}' => $roomModel->title,
        ];

        // Send to buyers
        $buyers = $roomModel->getBuyers();
        foreach ($buyers as $user) {
            $tags['{USER_NAME}'] = $user->getFullName();
            $tags['{ROOM_LINK}'] = $roomModel->detailedRoom->getUrl();

            self::sendNotifyToUser($user, self::EVENT_DOCUMENT_ADDED_TO_ROOM, $tags);
        }

        // Send to admin OR to manager
        if (Yii::$app->user->identity->type == User::TYPE_MANAGER) {
            $adminModel = $roomModel->admin;

            $tags['{USER_NAME}'] = $adminModel->getFullName();
            $tags['{ROOM_LINK}'] = $roomModel->detailedRoom->getUrlBackend();

            self::sendNotifyToUser($adminModel, self::EVENT_DOCUMENT_ADDED_TO_ROOM, $tags);
        } else {
            $managerModel = $roomModel->user;

            $tags['{USER_NAME}'] = $managerModel->getFullName();
            $tags['{ROOM_LINK}'] = $roomModel->detailedRoom->getUrl();

            self::sendNotifyToUser($managerModel, self::EVENT_DOCUMENT_ADDED_TO_ROOM, $tags);
        }
    }

    /**
     * Sends a notification to all AJA admins.
     *
     * @param int $eventID
     * @param array $tags
     * @param array $attach
     * @return bool
     */
    protected static function sendNotifyToAja($eventID, $tags, $attach = array())
    {
        $sent = true;

        $adminModels = User::find()->active()->ofType(User::TYPE_ADMIN)->all();
        foreach ($adminModels as $adminModel) {
            $sent = self::sendNotifyToUser($adminModel, $eventID, $tags, $attach) && $sent;
        }

        return $sent;
    }
}