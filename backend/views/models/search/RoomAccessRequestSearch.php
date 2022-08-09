<?php

namespace backend\modules\dataroom\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\dataroom\models\RoomAccessRequest;

class RoomAccessRequestSearch extends RoomAccessRequest
{
    public $userEmail;
    public $userFirstName;
    public $userLastName;
    public $userCompany;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userEmail', 'userFirstName', 'userLastName', 'userCompany', 'createdDate', 'status'], 'safe'],
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
    public function searchForRoom($roomID, $params)
    {
        $query = RoomAccessRequest::find()
            ->joinWith(['user'])
            ->andWhere(['roomID' => $roomID, 'User.isRemoved' => 0])
            ->orderBy('RoomAccessRequest.id DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageParam' => 'access-request-page',
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['status' => $this->status]);

        $query
            ->andFilterWhere(['like', 'User.email', $this->userEmail])
            ->andFilterWhere(['like', 'User.firstName', $this->userFirstName])
            ->andFilterWhere(['like', 'User.lastName', $this->userLastName])
            ->andFilterWhere(['like', 'User.companyName', $this->userCompany])
            ->andFilterWhere(['like', 'RoomAccessRequest.createdDate', $this->createdDate]);;

        return $dataProvider;
    }
}
