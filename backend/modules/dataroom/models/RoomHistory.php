<?php

namespace backend\modules\dataroom\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * This is the model class for table "RoomHistory".
 *
 * @property integer $id
 * @property integer $roomID
 * @property integer $userID
 * @property integer $hasFullAccess
 * @property integer $ip
 * @property string $createdDate
 *
 * @property Room $room
 * @property User $user
 */
class RoomHistory extends \yii\db\ActiveRecord
{
    const SCENARIO_SEARCH = 'search';
    
    public $userEmail;
    public $userFirstName;
    public $userLastName;
    public $userCompany;
    public $status;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'RoomHistory';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdDate',
                'updatedAtAttribute' => null,
                'value' => function() {
                    return date('Y-m-d H:i:s');
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        
        $scenarios[self::SCENARIO_SEARCH] = ['userEmail', 'userFirstName', 'userLastName', 'userCompany', 'status', 'createdDate'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['roomID', 'ip'], 'required'],
            [['roomID', 'userID', 'ip'], 'integer'],
            ['hasFullAccess', 'boolean'],
            [['createdDate'], 'safe'],
            [['roomID'], 'exist', 'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['roomID' => 'id']],
            [['userID'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userID' => 'id']],

            [['userEmail', 'userFirstName', 'userLastName', 'userCompany', 'status', 'createdDate'], 'safe', 'on' => self::SCENARIO_SEARCH]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'roomID' => 'Room ID',
            'userID' => 'User ID',
            'hasFullAccess' => 'Has Full Access',
            'ip' => 'IP',
            'createdDate' => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'roomID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userID']);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchForRoom($roomID, $params = [])
    {
        $this->scenario = self::SCENARIO_SEARCH;

        $query = self::find()
            ->joinWith(['user'])
            ->andWhere(['roomID' => $roomID])
            ->orderBy('RoomHistory.createdDate DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageParam' => 'room-history-page',
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query
            ->andFilterWhere(['like', 'User.email', $this->userEmail])
            ->andFilterWhere(['like', 'User.firstName', $this->userFirstName])
            ->andFilterWhere(['like', 'User.lastName', $this->userLastName])
            ->andFilterWhere(['like', 'User.companyName', $this->userCompany])
            ->andFilterWhere(['like', 'RoomHistory.createdDate', $this->createdDate]);;

        return $dataProvider;
    }
}