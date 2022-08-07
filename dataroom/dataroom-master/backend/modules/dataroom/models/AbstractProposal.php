<?php

namespace backend\modules\dataroom\models;

use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use common\models\User;

abstract class AbstractProposal extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE_ADMIN = 'create-admin';

    public $userID;

    /**
     * Returns path of template that buyer should fill and attach to proposal form.
     * 
     * @return string
     */
    abstract function templatePath();
    abstract function getUrl();

    public static function primaryKey()
    {
        return ['proposalID'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['proposalID'], 'integer'],
            [['proposalID'], 'exist', 'skipOnError' => true, 'targetClass' => Proposal::className(), 'targetAttribute' => ['proposalID' => 'id']],

            ['userID', 'integer'],
            ['userID', 'required', 'on' => self::SCENARIO_CREATE_ADMIN],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProposal()
    {
        return $this->hasOne(Proposal::className(), ['id' => 'proposalID']);
    }

    /**
     * Get users for proposal creation form.
     * 
     * @param  integer|null $roomId
     * @return array
     */
    public function getUserList($roomId = null)
    {
        $query = User::find()->where([
            'type' => User::TYPE_USER,
            'isActive' => 1,
            'isConfirmed' => 1,
        ]);

        if ($roomId) {

            $query->innerJoinWith(['roomAccessRequest' => function (ActiveQuery $q) use ($roomId) {
                $q->andOnCondition(['RoomAccessRequest.roomID' => $roomId]);
                $q->andOnCondition(['IS NOT', 'validatedBy', null]);
            }]);
        }

        $users = $query->all();

        return ArrayHelper::map($users, 'id', 'email');
    }
}