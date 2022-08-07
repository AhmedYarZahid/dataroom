<?php

namespace backend\modules\dataroom\controllers;

use Yii;
use console\controllers\ConsoleController as BaseConsoleController;
use yii\db\Expression;
use yii\helpers\Console;
use backend\modules\dataroom\Module as DataroomModule;
use backend\modules\dataroom\RoomManager;
use backend\modules\dataroom\models\Room;
use backend\modules\dataroom\models\RoomCompany;
use backend\modules\notify\models\Notify;
use Carbon\Carbon;

class ConsoleController extends BaseConsoleController
{
    protected $logCategory = DataroomModule::LOG_CATEGORY;

    protected $roomManager;

    public function __construct($id, $controller, RoomManager $roomManager, $config = [])
    {
        $this->roomManager = $roomManager;

        parent::__construct($id, $controller, $config);
    }

    /**
     * Finds and publishes scheduled rooms.
     */
    public function actionPublishRooms()
    {
        $this->updateRoomStatus('publicationDate', [Room::STATUS_DRAFT], Room::STATUS_PUBLISHED, 'dataroom/publish-rooms');
    }

    /**
     * Finds and sets expired status for scheduled rooms.
     */
    public function actionExpireRooms()
    {
        $this->updateRoomStatus('expirationDate', [Room::STATUS_DRAFT, Room::STATUS_PUBLISHED], Room::STATUS_EXPIRED, 'dataroom/expire-rooms');
    }

    /**
     * Finds and sets archived status for scheduled rooms.
     */
    public function actionArchiveRooms()
    {
        $this->updateRoomStatus('archivationDate', [Room::STATUS_DRAFT, Room::STATUS_PUBLISHED, Room::STATUS_EXPIRED], Room::STATUS_ARCHIVED, 'dataroom/archive-rooms');
    }

    /**
     * Sends notifications about rooms that will be published soon.
     */
    public function actionNotifyPublication()
    {
        $date = Carbon::now()->addHours(72)->toDateTimeString();
        $now = Carbon::now()->toDateTimeString();

        $rooms = Room::find()
            ->where(['<=', 'publicationDate', $date])
            ->andWhere(['>', 'publicationDate', $now])
            ->andWhere(['status' => Room::STATUS_DRAFT, 'publicationAlertSent' => 0])
            ->all();
        $count = count($rooms);

        $this->outputMessage("[dataroom/notify-publication] $count rooms were found.", "\n");
        
        foreach ($rooms as $room) {
            $room->publicationAlertSent = 1;
            $room->save(false);
            Notify::sendRoomPublishingSoon($room->detailedRoom);
        }
    }

    /**
     * Sends notifications about rooms that will be expired soon.
     */
    public function actionNotifyExpiration()
    {
        $date = Carbon::now()->addHours(48)->toDateTimeString();
        $now = Carbon::now()->toDateTimeString();

        $rooms = Room::find()
            ->where(['<=', 'expirationDate', $date])
            ->andWhere(['>', 'expirationDate', $now])
            ->andWhere(['status' => Room::STATUS_PUBLISHED, 'expirationAlertSent' => 0])
            ->all();
        $count = count($rooms);

        $this->outputMessage("[dataroom/notify-expiration] $count rooms were found.", "\n");

        foreach ($rooms as $room) {
            $room->expirationAlertSent = 1;
            $room->save(false);
            Notify::sendRoomExpiringSoon($room->detailedRoom);
        }
    }

    /**
     * Sends notification on the date of the review hearing offers.
     */
    public function actionNotifyHearing()
    {
        $date = Carbon::now()->toDateString();

        $rooms = RoomCompany::find()
            ->joinWith('room')
            ->where(['=', 'hearingDate', $date])
            ->andWhere(['hearingAlertSent' => 0])
            ->andWhere(['in', 'Room.status', [Room::STATUS_PUBLISHED, Room::STATUS_EXPIRED]])
            ->all();
        $count = count($rooms);

        $this->outputMessage("[dataroom/notify-hearing] $count rooms were found.", "\n");
        
        foreach ($rooms as $room) {
            $room->hearingAlertSent = 1;
            $room->save(false);
            Notify::sendHearingSoon($room);
        }
    }

    /**
     * Sends notification about rooms that were updated.
     *
     * @deprecated
     */
    /*public function actionNotifyUpdates()
    {
        $rooms = Room::find()
            ->andWhere(['pendingUpdateAlert' => 1])
            ->andWhere(['!=', 'Room.status', Room::STATUS_ARCHIVED])
            ->all();
        $count = count($rooms);

        $this->outputMessage("[dataroom/notify-updates] $count rooms were found.", "\n");
        
        foreach ($rooms as $room) {
            $room->pendingUpdateAlert = 0;
            $room->save(false);

            Notify::sendRoomUpdatedToAja($room->detailedRoom);

            if ($room->status != Room::STATUS_DRAFT) {
                Notify::sendRoomUpdatedToBuyers($room->detailedRoom);
            }
        }
    }*/

    /**
     * Updates status for scheduled rooms.
     * 
     * @param  string $dateField   Name of the db date field to look for.
     * @param  array  $oldStatuses Statuses to search for.
     * @param  string $newStatus   Status to set.
     * @param  string $actionName  Name of the action for logging purposes.
     */
    protected function updateRoomStatus($dateField, $oldStatuses, $newStatus, $actionName)
    {
        $today = $dateField == 'expirationDate'
            ? date('Y-m-d H:i')
            : Carbon::now()->toDateString();

        $query = Room::find();

        if ($dateField == 'expirationDate') {
            $query->andWhere(['=', new Expression('DATE_FORMAT(' . $dateField . ', "%Y-%m-%d %H:%i")'), $today]);
        } else {
            $query->andWhere(['<=', new Expression('DATE(' . $dateField . ')'), $today]);
        }

        $rooms = $query->andWhere(['status' => $oldStatuses])
            ->all();

        $count = count($rooms);

        $this->outputMessage("[$actionName] $count rooms were found.", "\n");

        $countUpdated = 0;
        foreach ($rooms as $room) {
            if ($this->roomManager->updateRoomStatus($room, $newStatus)) {
                $countUpdated++;

                if ($room->section != DataroomModule::SECTION_CV) {
                    switch ($newStatus) {
                        case Room::STATUS_EXPIRED:
                            Notify::sendRoomExpired($room->detailedRoom);
                            break;

                        case Room::STATUS_ARCHIVED:
                            Notify::sendRoomArchived($room->detailedRoom);
                            break;
                    }
                }
            }
        }

        $this->outputMessage("[$actionName] $countUpdated rooms were updated.", "\n");
    }
}