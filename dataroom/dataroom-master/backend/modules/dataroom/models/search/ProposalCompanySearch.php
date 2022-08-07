<?php

namespace backend\modules\dataroom\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\dataroom\models\ProposalCompany;

class ProposalCompanySearch extends ProposalCompany
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
            [['proposalID', 'roomTitle', 'userEmail', 'creatorEmail', 'createdDate'], 'safe'],
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
        $with = ['doc'];

        $query = ProposalCompany::find()
            ->joinWith($joinWith)
            ->with($with)
            ->orderBy('Proposal.createdDate DESC');

        if (!empty($params['roomID'])) {
            $query->andWhere(['Room.id' => $params['roomID']]);
        }    

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 100,
            ],
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
}
