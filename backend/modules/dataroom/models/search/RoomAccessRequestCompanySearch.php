<?php

namespace backend\modules\dataroom\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\dataroom\models\RoomAccessRequestCompany;

class RoomAccessRequestCompanySearch extends RoomAccessRequestCompany
{
    public $roomTitle;
    public $userEmail;
    public $isValidated;
    public $validatedBy;
    public $createdDate;
    public $status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accessRequestID', 'roomTitle', 'userEmail', 'isValidated', 'validatedBy', 'createdDate', 'status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $with = [
            'kbisDoc', 'balanceSheetDoc', 'cniDoc', 'commitmentDoc',
        ];
        $joinWith = [
            'accessRequest', 'accessRequest.room', 'accessRequest.user', 'accessRequest.admin Admin',
        ];

        $query = RoomAccessRequestCompany::find()
            ->joinWith($joinWith)
            ->with($with)
            ->andWhere(['User.isRemoved' => 0])
            ->orderBy('RoomAccessRequest.id DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($this->isValidated === '1') {
            $query->andWhere(['not', ['RoomAccessRequest.validatedBy' => null]]);
        } else if ($this->isValidated === '0') {
            $query->andWhere(['RoomAccessRequest.validatedBy' => null]);
        }

        $query->andFilterWhere(['accessRequestID' => $this->accessRequestID]);
        $query->andFilterWhere(['RoomAccessRequest.status' => $this->status]);

        $query
            ->andFilterWhere(['like', 'Room.title', $this->roomTitle])
            ->andFilterWhere(['like', 'User.email', $this->userEmail])
            ->andFilterWhere(['like', 'Admin.email', $this->validatedBy])
            ->andFilterWhere(['like', 'RoomAccessRequest.createdDate', $this->createdDate]);;

        return $dataProvider;
    }
}
