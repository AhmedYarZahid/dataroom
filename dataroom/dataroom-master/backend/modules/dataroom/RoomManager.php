<?php

namespace backend\modules\dataroom;

use backend\modules\dataroom\models\RoomCV;
use backend\modules\notify\models\Notify;
use backend\modules\parameter\models\Parameter;
use Exception;
use Yii;
use backend\modules\dataroom\Module as DataroomModule;
use backend\modules\dataroom\exceptions\ManagerNotCreatedException;
use backend\modules\dataroom\models\Room;
use backend\modules\dataroom\models\AbstractDetailedRoom;
use backend\modules\dataroom\models\RoomCompany;
use common\models\User;
use Carbon\Carbon;

class RoomManager
{
    public $newDetailedRooms = [];

    protected $userManager;

    protected $newManagerCreated;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Creates a new room
     *
     * @param  Room $room
     * @param  AbstractDetailedRoom $detailedRoom
     * @return bool Whether the room was created.
     * @throws Exception
     * @throws ManagerNotCreatedException
     * @throws \yii\db\Exception
     */
    public function createRoom(Room $room, AbstractDetailedRoom $detailedRoom)
    {
        $room->section = $this->getRoomSection($detailedRoom);

        if ($room->section == DataroomModule::SECTION_CV) {
            // Set infinity dates for CV Rooms
            $room->publicationDate = date('Y-m-d', strtotime('+200 year'));
            $room->expirationDate = date('Y-m-d', strtotime('+201 year'));
            $room->archivationDate = date('Y-m-d', strtotime('+202 year'));

            $room->createdDate = date('Y-m-d H:i:s');
        } elseif ($room->section == DataroomModule::SECTION_COMPANIES) {
            // Set real expiry date
            if ($detailedRoom->refNumber2) {
                $room->expirationDate = $detailedRoom->refNumber2;
            } elseif ($detailedRoom->refNumber1) {
                $room->expirationDate = $detailedRoom->refNumber1;
            } else {
                $room->expirationDate = $detailedRoom->refNumber0;
            }
        }

        $roomValid = $room->validate();
        $detailedRoomValid = $detailedRoom->validate();
        
        if (!$roomValid || !$detailedRoomValid) {
            return false;
        }

        try {
            $transaction = Yii::$app->db->beginTransaction();

            $manager = $this->findOrCreateManager($room, $detailedRoom);

            $room->creatorID = Yii::$app->user->id;
            $room->userID = $manager->id;
            $this->setRoomStatus($room);
            $room->save(false);

            $detailedRoom->roomID = $room->id;
            $detailedRoom->save(false);

            $this->newDetailedRooms[] = $detailedRoom;

            if ($room->section == DataroomModule::SECTION_CV) {
                for ($i = 1; $i < $detailedRoom->roomsNumber; $i++) {
                    $clonedRoom = clone $room;
                    $clonedRoom->isNewRecord = true;
                    $clonedRoom->id = null;
                    $clonedRoom->save(false);

                    $clonedDetailedRoom = clone $detailedRoom;
                    $clonedDetailedRoom->isNewRecord = true;
                    $clonedDetailedRoom->id = null;
                    $clonedDetailedRoom->roomID = $clonedRoom->id;
                    $clonedDetailedRoom->save(false);

                    $this->newDetailedRooms[] = $clonedDetailedRoom;
                }
            }

            $transaction->commit();

            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            
            if (!($e instanceof ManagerNotCreatedException)) {
                Yii::error($e->__toString(), DataroomModule::LOG_CATEGORY);
            }

            throw $e;
        }

        return false;
    }

    /**
     * Updates a room
     *
     * @param  Room $room
     * @param  AbstractDetailedRoom $detailedRoom
     * @return bool Whether the room was updated.
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function updateRoom(Room $room, AbstractDetailedRoom $detailedRoom)
    {
        // Set necessary dates for CV Rooms only if room was updated first time
        $cvPublishedNotify = false;
        if ($room->section == DataroomModule::SECTION_CV && in_array($detailedRoom->state, [RoomCV::STATE_TO_FILL, RoomCV::STATE_TO_CORRECT])) {
            // Set publication date +72 hours (for CV Rooms)
            $cvRoomPublishPeriod = Parameter::getCVRoomPublishPeriod();
            $room->publicationDate = date('Y-m-d H:i:s', strtotime('+' . $cvRoomPublishPeriod . ' hour'));

            // Set expiration and archivation date +6 months (for CV Rooms)
            $cvRoomExpirationPeriod = Parameter::getCVRoomExpirationPeriod();
            $room->expirationDate = date('Y-m-d H:i:s', strtotime('+' . $cvRoomPublishPeriod . ' hour ' . $cvRoomExpirationPeriod . ' month'));
            $room->archivationDate = date('Y-m-d H:i:s', strtotime('+' . $cvRoomPublishPeriod . ' hour ' . $cvRoomExpirationPeriod . ' month 1 day'));

            $detailedRoom->state = RoomCV::STATE_READY;

            $cvPublishedNotify = Yii::$app->user->id != $room->creatorID;
        } elseif ($room->section == DataroomModule::SECTION_COMPANIES) {
            // Set real expiry date
            if ($detailedRoom->refNumber2) {
                $room->expirationDate = $detailedRoom->refNumber2;
            } elseif ($detailedRoom->refNumber1) {
                $room->expirationDate = $detailedRoom->refNumber1;
            } else {
                $room->expirationDate = $detailedRoom->refNumber0;
            }
        }

        $roomValid = $room->validate();
        $detailedRoomValid = $detailedRoom->validate();


        if (!$roomValid || !$detailedRoomValid) {
            return false;
        }

        try {
            $transaction = Yii::$app->db->beginTransaction();

            $this->setRoomStatus($room);
            
            //$room->pendingUpdateAlert = 1;

            // Uncomment if dirty attrs check is needed.
            /*if (!$room->pendingUpdateAlert) {
                $dirtyAttrs = $room->getDirtyAttributes();
                $dirtyAttrsDetailed = $detailedRoom->getDirtyAttributes();

                $room->pendingUpdateAlert = !empty($dirtyAttrs) || !empty($dirtyAttrsDetailed); 
            }*/

            $room->save(false);
            $detailedRoom->save(false);

            if ($room->status != Room::STATUS_ARCHIVED) {
                Notify::sendRoomUpdatedToAja($detailedRoom);

                if ($room->status != Room::STATUS_DRAFT) {
                    Notify::sendRoomUpdatedToBuyers($detailedRoom);
                }
            }


            // Send notify about newly uploaded CV (to room creator)
            if ($cvPublishedNotify) {
                Notify::sendRoomCVUploaded($detailedRoom);
            }

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();

            Yii::error($e->__toString(), DataroomModule::LOG_CATEGORY);

            throw $e;
        }

        return false;
    }

    /**
     * Updates status of the room.
     * 
     * @param  Room   $room
     * @param  string $status
     * @return bool   Whether the room model was updated.
     */
    public function updateRoomStatus(Room $room, $status)
    {
        $room->status = $status;
        $saved = $room->save(false);

        // send a notification?
        
        return $saved;
    }

    /**
     * @return boolean Whether a new manager was created during the room creation.
     */
    public function newManagerCreated()
    {
        return $this->newManagerCreated;
    }

    /**
     * Deactivate CV Room
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param RoomCV $detailedRoom
     * @return bool
     */
    public function deactivateCVRoom(RoomCV $detailedRoom)
    {
        $detailedRoom->state = RoomCV::STATE_TO_CORRECT;
        $detailedRoom->save(false);

        // Set infinity dates for CV Rooms
        $detailedRoom->room->publicationDate = date('Y-m-d', strtotime('+200 year'));
        $detailedRoom->room->expirationDate = date('Y-m-d', strtotime('+201 year'));
        $detailedRoom->room->archivationDate = date('Y-m-d', strtotime('+202 year'));

        $this->setRoomStatus($detailedRoom->room);

        $detailedRoom->room->save(false);

        if (Yii::$app->user->id != $detailedRoom->room->userID) {
            Notify::sendRoomCVToCorrect($detailedRoom);
        }

        return true;
    }

    /**
     * Sets room status depending on the publication/expiration/archivation dates.
     * 
     * @param Room $room
     */
    protected function setRoomStatus(Room $room)
    {
        $now = Carbon::now();

        $room->status = Room::STATUS_DRAFT;

        if ($room->publicationDate <= $now) {
            $room->status = Room::STATUS_PUBLISHED;
        }

        if ($room->expirationDate <= $now) {
            $room->status = Room::STATUS_EXPIRED;
        }

        if ($room->archivationDate <= $now) {
            $room->status = Room::STATUS_ARCHIVED;
        }
    }

    /**
     * Finds or creates a user.
     * 
     * @param  Room $room
     * @param  AbstractDetailedRoom $detailedRoom
     * @throws ManagerNotCreatedException
     * @return User User model.
     */
    protected function findOrCreateManager($room, AbstractDetailedRoom $detailedRoom)
    {
        if ($room->userID) {
            $user = User::findOne($room->userID);
        } else {
            $user = new User;
            $user->email = $room->userEmail;
            $user->lastName = $room->userName;
            $user->firstName = $room->userFirstName;
            $user->profession = $room->userProfession;
            $user->type = $user::TYPE_MANAGER;
            $user->generateOneTimeLoginToken();

            $this->newManagerCreated = true;
        }

        $user->scenario = 'create-room';
        $section = $this->getRoomSection($detailedRoom);

        if (!$this->userManager->save($user, $section, true)) {
            $error = ['message' => "Can't create a User model", 'errors' => $user->getErrors()];
            Yii::error($error, DataroomModule::LOG_CATEGORY);

            throw new ManagerNotCreatedException;
        }

        return $user;
    }

    /**
     * Returns section related to a given room.
     * 
     * @param  AbstractDetailedRoom $detailedRoom
     * @return string Section name.
     */
    protected function getRoomSection(AbstractDetailedRoom $detailedRoom)
    {
        return $detailedRoom->getDataroomSection();
    }
}