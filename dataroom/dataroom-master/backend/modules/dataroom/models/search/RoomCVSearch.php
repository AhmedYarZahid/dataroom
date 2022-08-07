<?php

namespace backend\modules\dataroom\models\search;

use backend\modules\dataroom\models\RoomAccessRequest;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\dataroom\models\RoomCV;

/**
 * RoomCVSearch represents the model behind the search form about `backend\modules\dataroom\models\RoomCV`.
 */
class RoomCVSearch extends RoomCV
{
    public $roomId;
    public $roomTitle;
    public $roomStatus;
    public $managerEmail;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'roomID', 'subFunctionID', 'cvID'], 'integer'],
            [['roomId', 'roomTitle', 'roomStatus', 'managerEmail', 'companyName', 'candidateProfile', 'firstName', 'lastName', 'address', 'email', 'phone', 'seniority', 'departmentID', 'regionID', 'activityDomainID', 'functionID'], 'safe'],
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
        $query = RoomCVSearch::find()
            ->joinWith(['room', 'room.user'])
            ->orderBy('Room.id DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'Room.id' => $this->roomId,
            'Room.status' => $this->roomStatus,
        ]);

        $query
            ->andFilterWhere(['like', 'Room.title', $this->roomTitle])
            ->andFilterWhere(['like', 'User.email', $this->managerEmail]);

        return $dataProvider;
    }

    /**
     * @param  array $params
     * @return ActiveDataProvider
     */
    public function searchPublished($params = [])
    {
        $query = RoomCV::find()
            ->joinWith([
                'room' => function ($q) {
                    $q->published(Yii::$app->user->isGuest);
                }
            ])
            ->orderBy('Room.publicationDate DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'Room.id' => $this->roomID,
            'regionID' => $this->regionID,
            'departmentID' => $this->departmentID,
            'activityDomainID' => $this->activityDomainID,
            'functionID' => $this->functionID,
        ]);

        return $dataProvider;
    }

    /**
     * @param  User $user
     * @return ActiveDataProvider|null
     */
    public function searchUserRooms(User $user)
    {
        switch ($user->type) {
            case User::TYPE_USER:
                return $this->searchBuyerRooms($user);

            case User::TYPE_MANAGER:
                return $this->searchManagerRooms($user);

            default:
                return null;
        }
    }

    /**
     * @return RoomCV[]
     */
    public function getLatestOffers()
    {
        $query = RoomCV::find()
            ->joinWith([
                'room' => function ($q) {
                    $q->published();
                }
            ])
            ->orderBy('Room.publicationDate DESC')
            ->limit(5);

        return $query->all();
    }

    /**
     * Finds rooms the buyer asked/has access to.
     *
     * @param  User $user
     * @return ActiveDataProvider
     */
    protected function searchBuyerRooms(User $user)
    {
        $roomIds = RoomAccessRequest::find()
            ->select('roomID')
            ->where(['userID' => $user->id])
            ->column();

        $query = RoomCV::find()
            ->joinWith(['room' => function ($q) use ($roomIds) {
                $q->published()->andWhere(['Room.id' => $roomIds]);
            }])
            ->with('room.currentUserAccessRequest')
            ->orderBy('Room.publicationDate DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
        ]);

        return $dataProvider;
    }

    /**
     * Finds rooms assigned to a given manager.
     *
     * @param  User $user
     * @return ActiveDataProvider
     */
    protected function searchManagerRooms(User $user)
    {
        $query = RoomCV::find()
            ->joinWith(['room' => function ($q) use ($user) {
                $q->andWhere(['Room.userID' => $user->id]);
            }])
            ->orderBy('Room.publicationDate DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
        ]);

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
        $query = RoomCV::find();

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
            'id' => $this->id,
            'roomID' => $this->roomID,
            'activityDomainID' => $this->activityDomainID,
            'functionID' => $this->functionID,
            'subFunctionID' => $this->subFunctionID,
            'cvID' => $this->cvID,
            'departmentID' => $this->departmentID,
            'regionID' => $this->regionID,
        ]);

        $query->andFilterWhere(['like', 'companyName', $this->companyName])
            ->andFilterWhere(['like', 'candidateProfile', $this->candidateProfile])
            ->andFilterWhere(['like', 'firstName', $this->firstName])
            ->andFilterWhere(['like', 'lastName', $this->lastName])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'seniority', $this->seniority]);

        return $dataProvider;
    }*/
}
