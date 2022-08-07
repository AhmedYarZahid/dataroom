<?php

namespace backend\modules\dataroom\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\dataroom\models\RoomAccessRequestRealEstate;

/**
 * RoomAccessRequestRealEstateSearch represents the model behind the search form about `backend\modules\dataroom\models\RoomAccessRequestRealEstate`.
 */
class RoomAccessRequestRealEstateSearch extends RoomAccessRequestRealEstate
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
            [['accessRequestID', 'identityCardID', 'cvID', 'lastTaxDeclarationID', 'kbisID', 'registrationsUpdatedStatusID', 'latestCertifiedAccountsID', 'capitalAllocationID'], 'integer'],
            [['personType', 'candidatePresentation', 'companyPresentation'], 'safe'],
            [['roomTitle', 'userEmail', 'isValidated', 'validatedBy', 'createdDate', 'status'], 'safe'],
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
            'identityCard', 'cv', 'lastTaxDeclaration', 'kbis', 'registrationsUpdatedStatus',
            'latestCertifiedAccounts', 'capitalAllocation'
        ];
        $joinWith = [
            'accessRequest', 'accessRequest.room', 'accessRequest.user', 'accessRequest.admin Admin',
        ];

        $query = RoomAccessRequestRealEstate::find()
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    /*public function search($params)
    {
        $query = RoomAccessRequestRealEstate::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'accessRequestID' => $this->accessRequestID,
            'identityCardID' => $this->identityCardID,
            'cvID' => $this->cvID,
            'lastTaxDeclarationID' => $this->lastTaxDeclarationID,
            'kbisID' => $this->kbisID,
            'registrationsUpdatedStatusID' => $this->registrationsUpdatedStatusID,
            'latestCertifiedAccountsID' => $this->latestCertifiedAccountsID,
            'capitalAllocationID' => $this->capitalAllocationID,
        ]);

        $query->andFilterWhere(['like', 'personType', $this->personType])
            ->andFilterWhere(['like', 'candidatePresentation', $this->candidatePresentation])
            ->andFilterWhere(['like', 'companyPresentation', $this->companyPresentation]);

        return $dataProvider;
    }*/
}
