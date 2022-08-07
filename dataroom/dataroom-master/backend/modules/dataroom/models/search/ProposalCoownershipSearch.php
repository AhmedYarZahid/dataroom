<?php

namespace backend\modules\dataroom\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\dataroom\models\ProposalCoownership;

/**
 * ProposalCoownershipSearch represents the model behind the search form about `backend\modules\dataroom\models\ProposalCoownership`.
 */
class ProposalCoownershipSearch extends ProposalCoownership
{
    public $roomTitle;
    public $userEmail;
    public $creatorEmail;
    public $createdDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['proposalID', 'documentID', 'kbisID', 'cniID', 'businessCardID'], 'integer'],
            [['companyName', 'fullName', 'address', 'email', 'phone', 'roomTitle', 'userEmail', 'creatorEmail', 'createdDate'], 'safe'],
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
        $joinWith = ['proposal', 'proposal.room', 'proposal.user user', 'proposal.creator creator'];
        $with = ['document', 'kbis', 'cni', 'businessCard'];

        $query = ProposalCoownership::find()
            ->innerJoinWith($joinWith)
            ->with($with)
            ->orderBy('Proposal.createdDate DESC');

        if (!empty($params['roomID'])) {
            $query->andWhere(['Room.id' => $params['roomID']]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['proposalID' => $this->proposalID]);

        $query
            ->andFilterWhere(['like', 'Room.title', $this->roomTitle])
            ->andFilterWhere(['like', 'user.email', $this->userEmail])
            ->andFilterWhere(['like', 'creator.email', $this->creatorEmail])
            ->andFilterWhere(['like', 'Proposal.createdDate', $this->createdDate]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param integer $roomID Room id
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchForRoom($roomID, $params)
    {
        $params['roomID'] = $roomID;

        return $this->search($params);
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
        $query = ProposalCoownership::find();

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
            'proposalID' => $this->proposalID,
            'documentID' => $this->documentID,
            'kbisID' => $this->kbisID,
            'cniID' => $this->cniID,
            'businessCardID' => $this->businessCardID,
        ]);

        $query->andFilterWhere(['like', 'companyName', $this->companyName])
            ->andFilterWhere(['like', 'fullName', $this->fullName])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }*/
}
