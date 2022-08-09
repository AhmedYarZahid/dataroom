<?php

namespace backend\modules\dataroom\models\search;

use backend\modules\dataroom\models\RoomAccessRequest;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\dataroom\models\RoomCoownership;

/**
 * RoomCoownershipSearch represents the model behind the search form about `backend\modules\dataroom\models\RoomCoownership`.
 */
class RoomCoownershipSearch extends RoomCoownership
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
            [['id', 'roomID', 'regionID', 'lotsNumber', 'coownersNumber', 'mainLotsNumber', 'secondaryLotsNumber', 'employeesNumber', 'lastFinancialYearApprovedAccountsID', 'constructionYear', 'totalFloorsNumber', 'adPosition'], 'integer'],
            [['roomId', 'roomTitle', 'roomStatus', 'managerEmail', 'propertyType', 'address', 'zip', 'city', 'missionEndDate', 'coownershipName', 'isElevator', 'heatingType', 'heatingEnergy', 'quickDescription', 'detailedDescription', 'keywords', 'procedure', 'procedureContact', 'firstName', 'lastName', 'phone', 'fax', 'phoneMobile', 'email', 'availabilityDate', 'homePresence', 'visibility', 'offerAcceptanceCondition', 'individualAssetsPresence', 'presenceEndDate'], 'safe'],
            [['latitude', 'longitude'], 'number'],
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
        $query = RoomCoownership::find()
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
        $query = RoomCoownership::find()
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
            'propertyType' => $this->propertyType,
        ]);

        $query->andFilterWhere(['like', 'zip', $this->zip]);

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
     * @return RoomCoownership[]
     */
    public function getLatestOffers()
    {
        $query = RoomCoownership::find()
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

        $query = RoomCoownership::find()
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
        $query = RoomCoownership::find()
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
        $query = RoomCoownership::find();

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
            'regionID' => $this->regionID,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'missionEndDate' => $this->missionEndDate,
            'lotsNumber' => $this->lotsNumber,
            'coownersNumber' => $this->coownersNumber,
            'mainLotsNumber' => $this->mainLotsNumber,
            'secondaryLotsNumber' => $this->secondaryLotsNumber,
            'employeesNumber' => $this->employeesNumber,
            'lastFinancialYearApprovedAccountsID' => $this->lastFinancialYearApprovedAccountsID,
            'constructionYear' => $this->constructionYear,
            'totalFloorsNumber' => $this->totalFloorsNumber,
            'availabilityDate' => $this->availabilityDate,
            'presenceEndDate' => $this->presenceEndDate,
            'adPosition' => $this->adPosition,
        ]);

        $query->andFilterWhere(['like', 'propertyType', $this->propertyType])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'zip', $this->zip])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'coownershipName', $this->coownershipName])
            ->andFilterWhere(['like', 'isElevator', $this->isElevator])
            ->andFilterWhere(['like', 'heatingType', $this->heatingType])
            ->andFilterWhere(['like', 'heatingEnergy', $this->heatingEnergy])
            ->andFilterWhere(['like', 'quickDescription', $this->quickDescription])
            ->andFilterWhere(['like', 'detailedDescription', $this->detailedDescription])
            ->andFilterWhere(['like', 'keywords', $this->keywords])
            ->andFilterWhere(['like', 'procedure', $this->procedure])
            ->andFilterWhere(['like', 'procedureContact', $this->procedureContact])
            ->andFilterWhere(['like', 'firstName', $this->firstName])
            ->andFilterWhere(['like', 'lastName', $this->lastName])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'fax', $this->fax])
            ->andFilterWhere(['like', 'phoneMobile', $this->phoneMobile])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'homePresence', $this->homePresence])
            ->andFilterWhere(['like', 'visibility', $this->visibility])
            ->andFilterWhere(['like', 'offerAcceptanceCondition', $this->offerAcceptanceCondition])
            ->andFilterWhere(['like', 'individualAssetsPresence', $this->individualAssetsPresence]);

        return $dataProvider;
    }*/
}
