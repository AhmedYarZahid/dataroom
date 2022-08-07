<?php

namespace common\rbac;

use yii\rbac\Rule;

class AskForAccessRule extends Rule
{
    public $name = 'askForAccess';

    /**
     * User can ask for access if he doesn't have it already.
     *
     * @param string|int $user the user ID. This should be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to [[CheckAccessInterface::checkAccess()]].
     * @return bool a value indicating whether the rule permits the auth item it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if (!isset($params['room'])) {
            return false;
        }

        if (!$params['room']->isPublished() && !$params['room']->proposalsAllowed) {
            return false;
        }

        $alreadyAsked = $params['room']->getRoomAccessRequests()
            ->andWhere(['RoomAccessRequest.userID' => $user])
            ->exists();

        return !$alreadyAsked;
    }
}