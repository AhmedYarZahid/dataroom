<?php

namespace common\rbac;

use yii\rbac\Rule;

class UpdateOwnRoomRule extends RoomOwnerRule
{
    public $name = 'updateOwnRoom';

    public function execute($user, $item, $params)
    {
        return parent::execute($user, $item, $params) && !$params['room']->isArchived();
    }
}