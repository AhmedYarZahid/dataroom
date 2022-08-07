<?php

namespace common\rbac;

use yii\rbac\Rule;

class RoomPublishedRule extends Rule
{
    public $name = 'roomIsPublished';

    public function execute($user, $item, $params)
    {
        return isset($params['room']) ? $params['room']->publishedOrExpired() : false;
    }
}