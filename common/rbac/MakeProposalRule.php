<?php

namespace common\rbac;

use yii\rbac\Rule;

class MakeProposalRule extends Rule
{
    public $name = 'makeProposal';

    /**
     * User can make only one proposal per room he has access to.
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

        $accessApproved = $params['room']->getRoomAccessRequests()
            ->andWhere(['RoomAccessRequest.userID' => $user])
            ->andWhere(['not', ['RoomAccessRequest.validatedBy' => null]])
            ->exists();

        if (!$accessApproved) {
            return false;
        }

        $proposalExists = $params['room']->getProposals()
            ->andWhere(['Proposal.userID' => $user])
            ->exists();

        return !$proposalExists;
    }
}