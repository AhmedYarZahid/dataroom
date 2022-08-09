<?php

namespace backend\modules\dataroom\models;

use backend\modules\dataroom\models\queries\RoomQuery;
use Da\QrCode\QrCode;

/**
 * This is the model class for table "RoomCompany".
 *
 * @property integer $roomID
 *
 * @property Room $room
 */

abstract class AbstractDetailedRoom extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_UPDATE_FRONT = 'update-front';

    abstract function getDataroomSection();
    abstract function getDataroomSectionLabel();
    abstract function getUrl();

    /*public static function primaryKey()
    {
        return ['roomID'];
    }*/

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'roomID']);
    }

    /**
     * @return AbstractDetailedRoom
     */
    public function getPreviousRoom()
    {
        return $this->find()
            ->innerJoinWith(['room' => function (RoomQuery $q) {
                $q->published()->andWhere([
                    '<', 'publicationDate', $this->room->publicationDate
                ]);
            }])->orderBy('Room.publicationDate DESC')->one();
    }

    /**
     * @return AbstractDetailedRoom
     */
    public function getNextRoom()
    {
        return $this->find()
            ->innerJoinWith(['room' => function (RoomQuery $q) {
                $q->published()->andWhere([
                    '>', 'publicationDate', $this->room->publicationDate
                ]);
            }])->orderBy('Room.publicationDate ASC')->one();
    }

    public function getQrCodeSrc()
    {
        $qrCode = (new QrCode($this->url))
            ->setSize(150)
            ->setMargin(0);

        return $qrCode->writeDataUri();
    }
}