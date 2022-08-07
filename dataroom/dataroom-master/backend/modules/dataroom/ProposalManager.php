<?php

namespace backend\modules\dataroom;

use Exception;
use Yii;
use backend\modules\dataroom\Module as DataroomModule;
use backend\modules\dataroom\models\AbstractProposal;
use backend\modules\dataroom\models\Proposal;
use backend\modules\dataroom\models\Room;
use common\models\User;

class ProposalManager
{
    /**
     * Creates a proposal submitted by user.
     * 
     * @param  Room              $room          
     * @param  AbstractProposal  $proposal 
     * @param  User              $user User who submits the proposal.     
     * @return bool Whether the proposal was created.
     */
    public function createProposal(Room $room, AbstractProposal $proposal, User $user)
    {
        $valid = $proposal->validate();

        if ($valid) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $baseProposal = new Proposal([
                    'roomID' => $room->id,
                    'userID' => $user->id,
                    'creatorID' => $user->id,
                ]);
                $baseProposal->save(false);

                $proposal->proposalID = $baseProposal->id;
                $proposal->save(false);

                $transaction->commit();

                return true;
            } catch (Exception $e) {
                $transaction->rollBack();

                Yii::error($e->__toString(), DataroomModule::LOG_CATEGORY);
                throw $e;
            }
        }

        return false;
    }

    /**
     * Creates a proposal from back office.
     * 
     * @param  Room              $room          
     * @param  AbstractProposal  $proposal
     * @param  User              $user Admin who creates the proposal.
     * @return bool Whether the proposal was created.
     */
    public function createByAdmin(Room $room, AbstractProposal $proposal, User $user)
    {
        $proposal->scenario = AbstractProposal::SCENARIO_CREATE_ADMIN;

        if ($proposal->validate()) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $baseProposal = new Proposal([
                    'roomID' => $room->id,
                    'userID' => $proposal->userID,
                    'creatorID' => $user->id,
                ]);
                $baseProposal->save(false);

                $proposal->proposalID = $baseProposal->id;
                $proposal->save(false);

                $transaction->commit();

                return true;
            } catch (Exception $e) {
                $transaction->rollBack();

                Yii::error($e->__toString(), DataroomModule::LOG_CATEGORY);
                throw $e;
            }
        }

        return false;
    }
}