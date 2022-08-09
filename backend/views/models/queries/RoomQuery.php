<?php

namespace backend\modules\dataroom\models\queries;

use yii\db\ActiveQuery;
use backend\modules\dataroom\models\Room;

class RoomQuery extends ActiveQuery
{
    public function published($excludeExpired = false)
    {
        if ($excludeExpired) {
            $statuses = [Room::STATUS_PUBLISHED];
        } else {
            $statuses = [Room::STATUS_PUBLISHED, Room::STATUS_EXPIRED];
        }

        return $this->andWhere(['in', 'Room.status', $statuses]);
    }
}