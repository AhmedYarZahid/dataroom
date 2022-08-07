<?php

namespace backend\modules\dataroom\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\dataroom\models\RoomCompany;
use backend\modules\dataroom\models\RoomAccessRequest;
use common\models\User;

class RoomCompanySearch extends RoomCompany
{
    public $roomId;
    public $mandateNumber;
    public $roomTitle;
    public $roomStatus;
    public $managerEmail;
    public $adminEmail;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['roomId', 'mandateNumber', 'roomTitle', 'roomStatus', 'managerEmail', 'adminEmail', 'region', 'activity', 'activitysector', 'annualTurnover', 'contributors'], 'safe'],
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
        $query = RoomCompany::find()
            ->joinWith(['room', 'room.user', 'room.admin Admin'])
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
            'Room.mandateNumber' => $this->mandateNumber,
        ]);

        $query
            ->andFilterWhere(['like', 'Room.title', $this->roomTitle])
            ->andFilterWhere(['like', 'User.email', $this->managerEmail])
            ->andFilterWhere(['like', 'Admin.email', $this->adminEmail]);

        return $dataProvider;
    }

    /**
     * @param  array $params
     * @return ActiveDataProvider
     */
    public function searchPublished($params = [])
    {
        $query = RoomCompany::find()
            ->joinWith([
                'room' => function ($q) {
                    $q->published(Yii::$app->user->isGuest);
                }
            ])
            ->orderBy('Room.publicationDate DESC');


        if (!($this->load($params) && $this->validate())) {

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort' => false,
            ]);

            return $dataProvider;
        }

        $query->andFilterWhere([
            'Room.id' => $this->roomId,
            // 'contributors' => $this->contributors,
        ]);


        if($this->roomTitle){
            $query
            ->andFilterWhere(['like', 'Room.title', $this->roomTitle]);
            // ->andFilterWhere(['regex', 'Room.title', '%'.$this->roomTitle.'%']) ;
        }

        if($this->region)
            $query->andFilterWhere(['=', 'region', $this->region]);

        if($this->activitysector != ''){
            $query
            ->andFilterWhere(['like', 'activitysector', $this->activitysector]);
        }

        if($this->activity != ''){
            $query
            ->andFilterWhere(['like', 'activity', $this->activity]);
            // ->andFilterWhere(['regex', 'activity', '%'.$this->activity.'%']);
        }

        if($this->annualTurnover != '')
            // $query->andFilterWhere(['like', 'annualTurnover', $this->annualTurnover]);




        /**
         * @link Switch contributors ranges
         * @see $this->getContributorsRanges
         *
         * 1 => "< 1 M€",
         * 2 => "1 à 3 M€",
         * 3 => "3 à 10 M€",
         * 4 => "> 10 M€",
         */
        if($this->annualTurnover != ''){

            switch ($this->annualTurnover) {
                case 1 : // < 10 salariés
                    $query->andFilterWhere(['<', 'annualTurnover', 1000]);
                    break;

                case 2 : //10 à 50 salariés
                    $query->andFilterWhere(['between', 'annualTurnover', 1000, 3000]);
                    break;

                case 3 : // 50 à 100 salariés
                    $query->andFilterWhere(['between', 'annualTurnover', 3000, 10000]);
                    break;

                case 4 : // > 100 salariés"
                    $query->andFilterWhere(['>', 'annualTurnover', 10000]);
                    break;

                default:
                    break;
            }
        }


        /**
         * @link Switch contributors ranges
         * @see $this->getContributorsRanges
         */
        if($this->contributors != ''){

            switch ($this->contributors) {
                case 1 : // < 10 salariés
                    $query->andFilterWhere(['between', 'contributors', 1, 9]);
                    break;

                case 2 : //10 à 50 salariés
                    $query->andFilterWhere(['between', 'contributors', 10, 50]);
                    break;

                case 3 : // 50 à 100 salariés
                    $query->andFilterWhere(['between', 'contributors', 50, 100]);
                    break;

                case 4 : // > 100 salariés"
                    $query->andFilterWhere(['>', 'contributors', 100]);
                    break;

                default:
                    break;
            }
        }



        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
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
     * @return RoomCompany[]
     */
    public function getLatestOffers()
    {
        $query = RoomCompany::find()
            ->joinWith([
                'room' => function ($q) {
                    $q->published();
                }
            ])
            ->orderBy('RoomCompany.refNumber0 DESC')
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

        $query = RoomCompany::find()
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
        $query = RoomCompany::find()
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
     * Array of ranges for filters on front end form
     * @see Switch contributors ranges above
     * @return array
     */
    public function getContributorsRanges()
    {
        return [
            1 => "< 10 salariés",
            2 => "10 à 50 salariés",
            3 => "50 à 100 salariés",
            4 => "> 100 salariés"
        ];
    }

    /**
     * Array of ranges for filters on front end form
     * @see Switch contributors ranges above
     * @return array
     */
    public function getAnnualTurnoverRanges()
    {
        return [
            1 => "< 1 M€",
            2 => "1 à 3 M€",
            3 => "3 à 10 M€",
            4 => "> 10 M€",
        ];
    }
}