<?php

namespace backend\modules\document\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use common\models\User;
use backend\modules\dataroom\models\Room;
use common\helpers\ExecEnvironmentHelper;

/**
 * This is the model class for table "DocumentHistory".
 *
 * @property integer $id
 * @property integer $documentID
 * @property integer $roomID
 * @property integer $userID
 * @property integer $ip
 * @property string $createdDate
 *
 * @property Document $document
 * @property User $user
 * @property Room $room
 */
class DocumentHistory extends \yii\db\ActiveRecord
{
    const SCENARIO_SEARCH = 'search';

    public $documentName;
    public $userEmail;
    public $userFirstName;
    public $userLastName;
    public $userCompany;

    protected $roomDocumentTree;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'DocumentHistory';
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
        
        $scenarios[self::SCENARIO_SEARCH] = ['documentName', 'userEmail', 'userFirstName', 'userLastName', 'userCompany', 'createdDate'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip'], 'required'],
            [['documentID', 'roomID', 'userID', 'ip'], 'integer'],
            [['createdDate'], 'safe'],
            [['documentID'], 'exist', 'skipOnError' => true, 'targetClass' => Document::className(), 'targetAttribute' => ['documentID' => 'id']],
            [['userID'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userID' => 'id']],
            [['roomID'], 'exist', 'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['roomID' => 'id']],

            [['documentName', 'userEmail', 'userFirstName', 'userLastName', 'userCompany', 'createdDate'], 'safe', 'on' => self::SCENARIO_SEARCH],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'documentID' => 'Document ID',
            'userID' => 'User ID',
            'roomID' => 'Room ID',
            'ip' => 'IP',
            'createdDate' => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Document::className(), ['id' => 'documentID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'roomID']);
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
        $this->roomID = $roomID;
        $this->scenario = self::SCENARIO_SEARCH;

        $query = self::find()
            ->joinWith(['user', 'document'])
            ->andWhere(['DocumentHistory.roomID' => $roomID])
            ->orderBy('DocumentHistory.createdDate DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageParam' => 'download-history-page',
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query
            ->andFilterWhere(['like', 'Document.title', $this->documentName])
            ->andFilterWhere(['like', 'User.email', $this->userEmail])
            ->andFilterWhere(['like', 'User.firstName', $this->userFirstName])
            ->andFilterWhere(['like', 'User.lastName', $this->userLastName])
            ->andFilterWhere(['like', 'User.companyName', $this->userCompany])
            ->andFilterWhere(['like', 'RoomHistory.createdDate', $this->createdDate]);

        return $dataProvider;
    }

    /**
     * Adds history record.
     * 
     * @param  Document  $document
     * @param  User|null $user
     * @return boolean   Whether a new record was created.
     */
    public static function addRecord(Document $document, User $user = null)
    {
        $history = new DocumentHistory;
        $history->documentID = $document->id;
        $history->roomID = $document->roomID;
        $history->userID = $user && $user->id ? $user->id : null;
        $history->ip = ExecEnvironmentHelper::getUserIp(true);

        return $history->save();
    }

    /**
     * Adds history record.
     * 
     * @param  Room      $room
     * @param  User|null $user
     * @return boolean   Whether a new record was created.
     */
    public static function addArchiveRecord(Room $room, User $user = null)
    {
        $history = new DocumentHistory;
        $history->roomID = $room->id;
        $history->userID = $user && $user->id ? $user->id : null;
        $history->ip = ExecEnvironmentHelper::getUserIp(true);

        return $history->save();
    }

    /**
     * Returns document folder name.
     * 
     * @return string
     */
    public function getFolderName()
    {
        if ($this->document && $this->document->parentID) {
            $tree = $this->getDocumentTree();

            $path = $this->getFolderNameRecursive($this->document->id, $tree);
            array_pop($path);

            return $path ? implode(' / ', $path) : '';
        }

        return '';
    }

    protected function getFolderNameRecursive($needle, $haystack, $path = [])
    {
        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                if (isset($value['title'])) {
                    $path[] = $value['title'];
                }

                $path = $this->getFolderNameRecursive($needle, $value, $path);
                if ($path) {
                    return $path;
                }
            } else if ($key == 'key' && $value === $needle) {
                return $path;
            }
        }

        return false;
    }

    protected function getDocumentTree()
    {
        if (!$this->roomDocumentTree) {
            $this->roomDocumentTree = Document::getRoomDocumentsTree($this->roomID);
        }

        return $this->roomDocumentTree;
    }
}