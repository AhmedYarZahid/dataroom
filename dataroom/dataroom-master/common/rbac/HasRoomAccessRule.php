<?php

namespace common\rbac;

use yii\rbac\Rule;
use common\models\User;

class HasRoomAccessRule extends Rule
{
    public $name = 'hasRoomAccess';

    public function execute($user, $item, $params)
    {
        if (!isset($params['room'])) {
            return false;
        }

        $accessApproved = $params['room']->getRoomAccessRequests()
            ->andWhere(['RoomAccessRequest.userID' => $user])
            ->andWhere(['not', ['RoomAccessRequest.validatedBy' => null]])
            ->exists();

        return $accessApproved;
    }
}