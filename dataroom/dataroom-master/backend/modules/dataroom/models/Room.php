<?php

namespace backend\modules\dataroom\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii2mod\behaviors\CarbonBehavior;
use common\models\User;
use backend\modules\dataroom\models\queries\RoomQuery;
use backend\modules\dataroom\models\RoomHistory;
use backend\modules\document\models\Document;
use backend\modules\dataroom\Module as DataroomModule;
use common\helpers\ExecEnvironmentHelper;

/**
 * This is the model class for table "Room".
 *
 * @property integer $id
 * @property string $mandateNumber
 * @property integer $creatorID
 * @property integer $userID
 * @property integer $adminID
 * @property string $title
 * @property boolean $public
 * @property string $status
 * @property string $section
 * @property integer $proposalsAllowed
 * @property string $publicationDate
 * @property string $expirationDate
 * @property string $archivationDate
 * @property string $createdDate
 * @property string $updatedDate
 *
 * @property Proposal[] $proposals
 * @property User $creator
 * @property User $user
 * @property User $admin
 * @property RoomAccessRequest[] $roomAccessRequests
 * @property RoomAccessRequest[] $validatedRoomAccessRequests
 * @property RoomCompany $roomCompany
 * @property RoomRealEstate $roomRealEstate
 * @property Document[] $documents
 * @property Document[] $publishedDocuments
 */
class Room extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_EXPIRED = 'expired';
    const STATUS_ARCHIVED = 'archived';

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_UPDATE_FRONT = 'update-front';

    public $isNewManager;
    public $userEmail;
    public $userName;
    public $userFirstName;
    public $userProfession;
    public $userProfile;

    public $imageFiles;

    public static function find()
    {
        return new RoomQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Room';
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
                'updatedAtAttribute' => 'updatedDate',
                'value' => function() {
                    return date('Y-m-d H:i:s');
                }
            ],
            'carbon' => [
                'class' => CarbonBehavior::className(),
                'attributes' => [
                    'publicationDate',
                    'expirationDate',
                    'archivationDate',
                    'createdDate',
                    'updatedDate',
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = ['title', 'isNewManager', 'userID', 'adminID', 'userEmail', 'userName', 'userFirstName', 'mandateNumber', 'publicationDate', 'expirationDate', 'archivationDate', 'createdDate', 'public'];

        $scenarios[self::SCENARIO_UPDATE] = $scenarios['default'];

        if (Yii::$app->user->id && Yii::$app->user->identity->isAdmin()) {
            $scenarios[self::SCENARIO_UPDATE_FRONT] = $scenarios['default'];
        } else {
            $scenarios[self::SCENARIO_UPDATE_FRONT] = ['title'];
        }

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['creatorID', 'adminID', 'title', 'publicationDate', 'expirationDate', 'archivationDate', 'section'], 'required'],
            [['mandateNumber'], 'required', 'when' => function ($model) {
                return $model->section != DataroomModule::SECTION_CV || $model->scenario != self::SCENARIO_CREATE;
            }],
            [['creatorID', 'userID', 'adminID', 'isNewManager', 'userProfession'], 'integer'],
            [['status'], 'string'],
            [['status'], 'default', 'value' => 'draft'],
            [['createdDate', 'updatedDate'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['mandateNumber'], 'string', 'max' => 30],
            [['creatorID'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['creatorID' => 'id']],
            [['userID'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userID' => 'id']],

            [['userID'], 'required', 'except' => self::SCENARIO_CREATE],
            [['userID'], 'required', 'when' => function ($model) {
                return !$model->isNewManager;
            }, 'on' => self::SCENARIO_CREATE],

            [['userEmail', 'userName', 'userFirstName'], 'required', 'when' => function ($model) {
                return $model->isNewManager;
            }, 'on' => self::SCENARIO_CREATE],

            ['userEmail', 'email'],
            ['userEmail', 'validateUserEmail'],
            [['userName', 'userFirstName'], 'string', 'max' => 50],



            [['publicationDate'], 'validateDate'],
            [['proposalsAllowed','public'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'creatorID' => 'Creator ID',
            'mandateNumber' => Yii::t('admin', 'Mandate number'),
            'userID' => Yii::t('admin', 'Manager'),
            'adminID' => Yii::t('app', 'Admin AJA'),
            'title' => 'Nom du dossier',
            'public' => 'Mandat confidentiel',
            'status' => 'Status',
            'publicationDate' => "Date d'ouverture de la DataRoom",
            'expirationDate' => "Date limite de dépôt des offres",
            'archivationDate' => "Date de clôture de la DataRoom",
            'createdDate' => 'Date de création',
            'updatedDate' => 'Updated Date',
            'userEmail' => "Email de l'administré en charge de cette room",
            'userName' => "Nom de l'administré",
            'userFirstName' => "Prénom de l'administré",
            'userProfession' => "Profession de l'administré",
            'userProfile' => "Profil de l'administré",
            'imageFiles' => 'Images',
            'proposalsAllowed' => 'Toujours possible de faire une offre',
            'isNewManager' => Yii::t('admin', 'Create new manager?'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProposals()
    {
        return $this->hasMany(Proposal::className(), ['roomID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creatorID']);
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
    public function getAdmin()
    {
        return $this->hasOne(User::className(), ['id' => 'adminID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomAccessRequests()
    {
        return $this->hasMany(RoomAccessRequest::className(), ['roomID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValidatedRoomAccessRequests()
    {
        return $this->hasMany(RoomAccessRequest::className(), ['roomID' => 'id'])
            ->andOnCondition(['IS NOT', 'validatedBy', null]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrentUserAccessRequest()
    {
        return $this->hasOne(RoomAccessRequest::className(), ['roomID' => 'id'])
            ->andWhere(['RoomAccessRequest.userID' => Yii::$app->user->id]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomCompany()
    {
        return $this->hasOne(RoomCompany::className(), ['roomID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomRealEstate()
    {
        return $this->hasOne(RoomRealEstate::className(), ['roomID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomCoownership()
    {
        return $this->hasOne(RoomCoownership::className(), ['roomID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomCV()
    {
        return $this->hasOne(RoomCV::className(), ['roomID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(Document::className(), ['roomID' => 'id'])
            ->andWhere(['type' => Document::TYPE_ROOM_IMAGE, 'isFolder' => 0]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments()
    {
        return $this->hasMany(Document::className(), ['roomID' => 'id'])
            ->andWhere(['isFolder' => 0, 'type' => Document::TYPE_ROOM])->orderBy('rank ASC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPublishedDocuments()
    {
        return $this->hasMany(Document::className(), ['roomID' => 'id'])
            ->andWhere(['isFolder' => 0, 'type' => Document::TYPE_ROOM])->published()->orderBy('rank ASC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetailedRoom()
    {
        switch ($this->section) {
            case DataroomModule::SECTION_COMPANIES:
                return $this->getRoomCompany();

            case DataroomModule::SECTION_REAL_ESTATE:
                return $this->getRoomRealEstate();

            case DataroomModule::SECTION_COOWNERSHIP:
                return $this->getRoomCoownership();

            case DataroomModule::SECTION_CV:
                return $this->getRoomCV();
        }
    }

    public function validateUserEmail($attr)
    {
        $user = User::findOne(['email' => $this->$attr]);

        if ($user && !$user->isManager()) {
            $this->addError($attr, Yii::t('admin', 'This user is not a manager.'));
        }
    }

    public function validateDate($attr)
    {
        if ($this->publicationDate >= $this->expirationDate) {
            $this->addError('publicationDate', Yii::t('admin', "Publication date must be less than expiration date."));
        }
        if ($this->publicationDate >= $this->archivationDate) {
            $this->addError('publicationDate', Yii::t('admin', "Publication date must be less than archivation date."));
        }
        if ($this->expirationDate >= $this->archivationDate) {
            $this->addError('archivationDate', Yii::t('admin', "Archivation date must be greater than expiration date."));
        }
    }

    public function statusLabel()
    {
        $list = self::statusList();

        return isset($list[$this->status]) ? $list[$this->status] : null;
    }

    public static function statusList()
    {
        return [
            Room::STATUS_DRAFT => Yii::t('admin', 'Draft'),
            Room::STATUS_PUBLISHED => Yii::t('admin', 'Published'),
            Room::STATUS_EXPIRED => Yii::t('admin', 'Expired'),
            Room::STATUS_ARCHIVED => Yii::t('admin', 'Archived'),
        ];
    }

    public function isDraft()
    {
        return $this->status == self::STATUS_DRAFT;
    }

    public function isPublished()
    {
        return $this->status == self::STATUS_PUBLISHED;
    }

    public function isExpired()
    {
        return $this->status == self::STATUS_EXPIRED;
    }

    public function isArchived()
    {
        return $this->status == self::STATUS_ARCHIVED;
    }

    public function publishedOrExpired()
    {
        return $this->isPublished() || $this->isExpired();
    }

    /**
     * Returns array of buyers who made proposal in this room.
     *
     * @param  boolean $onlyActive
     * @return User[]
     */
    public function getBuyers($onlyActive = true)
    {
        $proposals = $this->getProposals()->with('user')->all();

        $buyers = [];
        foreach ($proposals as $proposal) {
            if ($onlyActive && !$proposal->user->isActive) {
                continue;
            }

            $buyers[$proposal->user->id] = $proposal->user;
        }

        return $buyers;
    }

    /**
     * Adds history record.
     *
     * @param  User|null $user
     * @return boolean Whether a new record was created.
     */
    public function addHistoryRecord(User $user = null)
    {
        $history = new RoomHistory;
        $history->roomID = $this->id;
        $history->userID = $user && $user->id ? $user->id : null;
        $history->hasFullAccess = Yii::$app->user->can('seeRoomDetails', ['room' => $this]);
        $history->ip = ExecEnvironmentHelper::getUserIp(true);

        return $history->save();
    }
}