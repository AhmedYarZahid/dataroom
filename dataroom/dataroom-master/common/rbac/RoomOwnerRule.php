<?php

namespace common\rbac;

use yii\rbac\Rule;

class RoomOwnerRule extends Rule
{
    public $name = 'isRoomOwner';

    public function execute($user, $item, $params)
    {
        return isset($params['room']) ? $params['room']->userID == $user : false;
    }
}