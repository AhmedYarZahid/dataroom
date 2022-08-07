<?php

namespace common\models;

use backend\modules\dataroom\models\ProfileCoownership;
use backend\modules\dataroom\models\ProfileCV;
use backend\modules\dataroom\models\ProfileRealEstate;
use backend\modules\dataroom\models\queries\RoomQuery;
use backend\modules\dataroom\models\RoomCV;
use mdm\admin\components\AccessControl;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii2mod\behaviors\CarbonBehavior;
use yii\web\IdentityInterface;
use yii\imagine\Image;
use yii\web\MethodNotAllowedHttpException;
use yii\web\UploadedFile;
use common\helpers\FileHelper;
use yii\helpers\Url;
use common\extensions\arhistory\models\ActiveRecordHistory;
use yii\web\UserEvent;
use backend\modules\dataroom\models\Room;
use backend\modules\dataroom\models\Proposal;
use backend\modules\dataroom\models\RoomAccessRequest;
use backend\modules\dataroom\models\ProfileCompany;
use Carbon\Carbon;
use backend\modules\dataroom\Module as DataroomModule;

/**
 * This is the model class for table "User".
 *
 * @property integer $id
 * @property string $email
 * @property string $passwordHash
 * @property string $authKey
 * @property string $confirmationCode
 * @property string $passwordResetToken
 * @property string $type
 * @property string $profession
 * @property string $companyName
 * @property string $activity
 * @property string $firstName
 * @property string $lastName
 * @property string $phone
 * @property string $phoneMobile
 * @property string $address
 * @property string $zip
 * @property string $city
 * @property string $logo
 * @property string $comment
 * @property integer $isConfirmed
 * @property integer $isActive
 * @property integer $isRemoved
 * @property string $tempEmail
 * @property integer $isMailingContact
 * @property string $createdDate
 * @property string $updatedDate
 *
 * @property UserHistory[] $userHistories
 * @property UserHistory[] $roomAccessRequest
 */
class User extends ActiveRecordHistory implements IdentityInterface
{
    const TYPE_USER = 'user';
    const TYPE_MANAGER = 'manager';
    const TYPE_ADMIN = 'admin';
    const TYPE_SUPERADMIN = 'superadmin';

    /**
     * @var string user real password
     */
    public $password;

    /**
     * @var string user password confirmation
     */
    public $passwordConfirm;

    /**
     * @var string path to old logo (to always have reference to old logo path)
     */
    public $oldLogo = '';

    public $dataroomSections = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'User';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!Yii::$app->request->isConsoleRequest) {
            Yii::$app->user->on(\yii\web\User::EVENT_AFTER_LOGIN, [$this, 'afterLogin']);
            Yii::$app->user->on(\yii\web\User::EVENT_AFTER_LOGOUT, [$this, 'afterLogout']);
        }
    }

    /**
     * Event "onAfterLogin"
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param UserEvent $event
     * @throws Exception
     */
    public function afterLogin(UserEvent $event)
    {
        UserHistory::addHistoryRecord(UserHistory::EVENT_LOGIN, $event->identity->getId(), $event->identity->getId());
    }

    /**
     * Event "onAfterLogout"
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param UserEvent $event
     * @throws Exception
     */
    public function afterLogout(UserEvent $event)
    {
        UserHistory::addHistoryRecord(UserHistory::EVENT_LOGOUT, $event->identity->getId(), $event->identity->getId());
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

        $scenarios['register'] = ['type','email', 'companyName', 'profession', 'activity', 'firstName', 'lastName', 'city', 'phone', 'phoneMobile', 'zip', 'address', 'birthPlace', 'logo', 'password', 'passwordConfirm', 'comment', 'isMailingContact', 'dataroomSections'];
        $scenarios['update-profile'] = ['companyName', 'profession', 'activity', 'city', 'phone', 'phoneMobile', 'zip', 'address', 'birthPlace', 'isMailingContact'];
        $scenarios['update-profile-admin'] = array_merge($scenarios['update-profile'], ['email', 'type', 'isActive', 'isConfirmed', 'comment', 'dataroomSections', 'firstName', 'lastName']);

        $scenarios['update-password'] = ['password', 'passwordConfirm'];
        $scenarios['request-reset-password'] = ['passwordResetToken'];
        $scenarios['reset-password'] = ['passwordHash', 'passwordResetToken'];

        $scenarios['create-room'] = ['email'];
        $scenarios['get-room-access'] = ['type', 'email', 'companyName', 'profession', 'activity', 'firstName', 'lastName', 'city', 'phone', 'phoneMobile', 'zip', 'address', 'birthPlace'];
        
        if ($this->isNewRecord) {
            $scenarios['get-room-access'][] = 'password';
            $scenarios['get-room-access'][] = 'passwordConfirm';
        }

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'email', 'firstName', 'lastName', 'password', 'passwordConfirm'], 'required'],
            [['passwordConfirm'], 'compare', 'compareAttribute' => 'password'],

            ['email', 'filter', 'filter' => 'trim'],
            [['email'], 'email'],
            [['email'], 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            [['isConfirmed', 'isActive', 'profession'], 'integer'],
            [['createdDate', 'updatedDate'], 'safe'],
            [['email', 'city', 'logo', 'tempEmail', 'birthPlace'], 'string', 'max' => 150],
            [['passwordHash'], 'string', 'max' => 255],
            [['authKey'], 'string', 'max' => 32],
            [['confirmationCode'], 'string', 'max' => 30],
            [['phoneMobile', 'phone'], 'string', 'length' => 10],
            [['passwordResetToken'], 'string', 'max' => 100],
            [['companyName', 'activity'], 'string', 'max' => 70],
            [['firstName', 'lastName'], 'string', 'max' => 50],
            [['address'], 'string', 'max' => 250],
            [['zip'], 'string', 'max' => 5],
            [['confirmationCode'], 'unique'],
            [['logo', 'comment'], 'safe'],
            [['logo'], 'file', 'extensions' => ['jpg','jpeg','gif','png']],
            ['isMailingContact', 'boolean'],

            /*['dataroomSections', 'required', 'when' => function ($model) {
                return $model->isBuyer() || $model->isManager();
            }],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
            'passwordHash' => Yii::t('app', 'Password Hash'),
            'authKey' => Yii::t('app', 'Auth Key'),
            'confirmationCode' => Yii::t('app', 'Confirmation Code'),
            'passwordResetToken' => Yii::t('app', 'Password Reset Token'),
            'companyName' => Yii::t('app', 'Company Name'),
            'firstName' => Yii::t('app', 'First Name'),
            'lastName' => Yii::t('app', 'Last Name'),
            'phoneMobile' => Yii::t('app', 'Phone Mobile'),
            'address' => Yii::t('app', 'Address'),
            'zip' => Yii::t('app', 'Zip'),
            'city' => Yii::t('app', 'City'),
            'logo' => Yii::t('app', 'Logo'),
            'isConfirmed' => Yii::t('app', 'Is Confirmed'),
            'isActive' => Yii::t('app', 'Is Active'),
            'tempEmail' => Yii::t('app', 'Temp Email'),
            'createdDate' => Yii::t('app', 'Created Date'),
            'updatedDate' => Yii::t('app', 'Updated Date'),
            'password' => Yii::t('app', 'Password'),
            'passwordConfirm' => Yii::t('app', 'Confirm Password'),
            'type' => Yii::t('app', 'Type'),
            'comment' => Yii::t('app', 'Comment'),
            'profession' => Yii::t('app', 'Profession'),
            'activity' => Yii::t('app', 'Activity'),
            'phone' => Yii::t('app', 'Phone'),
            'birthPlace' => Yii::t('app', 'Birth Place'),
            'isMailingContact' => Yii::t('app', 'I want to receive AJA news'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        $logoUploaded = false;
        if (in_array($this->scenario, ['update-profile', 'update-profile-admin'])) {
            $logoUploaded = $this->saveUploadedLogo();
        }

        if ($this->isNewRecord) {
            // Confirm all new accounts by default.
            if (empty($this->isConfirmed)) {
                $this->isConfirmed = 1;
            }

            // Generate a random password if none was provided.
            /*if (empty($this->passwordHash)) {
                $password = $this->password ? $this->password : Yii::$app->security->generateRandomString();
                $this->setPassword($password);
            }*/

            if (empty($this->confirmationCode)) {
                $this->generateConfirmationCode();
            }

            if (empty($this->authKey)) {
                $this->generateAuthKey();
            }
        }

        if ($result = parent::save($runValidation, $attributeNames)) {
            if ($logoUploaded) {
                $this->removeOldLogo();
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();

        $this->oldLogo = $this->logo;
    }

    /**
     * @inheritdoc
     */
    /*public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $userRole = Yii::$app->authManager->getRole($this->type);
            Yii::$app->authManager->assign($userRole, $this->id);
        }
    }*/

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'isActive' => 1, /*'type' => self::getUserTypes()*/]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new MethodNotAllowedHttpException('This functionality not allowed in this application.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['email' => $username, 'isActive' => 1, 'type' => self::getUserTypes()]);
    }

    /**
     * Finds user by id
     *
     * @param integer $id
     * @return User
     */
    public static function findByID($id)
    {
        return static::findOne(['id' => $id, 'isRemoved' => 0]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'passwordResetToken' => $token,
            'isActive' => 1,
            //'type' => self::getUserTypes()
        ]);
    }

    public static function findByOneTimeLoginToken($token)
    {
        return static::findOne([
            'oneTimeLoginToken' => $token,
            'isActive' => 1,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Generates confirmation code
     */
    public function generateConfirmationCode()
    {
        $this->confirmationCode = Yii::$app->security->generateRandomString(30);
    }

    /**
     * Generates access token (for auth via API)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @throws MethodNotAllowedHttpException
     */
    public function generateAccessToken()
    {
        throw new MethodNotAllowedHttpException('This functionality not allowed in this application.');
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->passwordHash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->passwordHash);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);

        return $timestamp + $expire >= time();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->passwordResetToken = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->passwordResetToken = null;
    }

    /**
     * Generates one-time login token.
     */
    public function generateOneTimeLoginToken()
    {
        $this->oneTimeLoginToken = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Move uploaded logo to the image folder
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return bool
     */
    public function saveUploadedLogo()
    {
        if ($logo = UploadedFile::getInstance($this, 'logo')) {
            $this->logo = FileHelper::getStorageStructure(\Yii::getAlias('@uploads/images/')) . Yii::$app->security->generateRandomString(27) . '.' . $logo->extension;
            $logo->saveAs(\Yii::getAlias('@uploads/images/') . $this->logo);

            Image::thumbnail('@uploads/images/' . $this->logo, Yii::$app->params['userLogoWidth'], Yii::$app->params['userLogoHeight'])
                ->save(Yii::getAlias('@uploads/images/' . $this->logo), ['quality' => 85]);

            return true;
        } else {
            $this->logo = $this->oldLogo;
        }

        return false;
    }

    /**
     * Return full path to the user logo
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param bool $relative
     * @return string
     */
    public function getLogoPath($relative = false)
    {
        $path = \Yii::getAlias('@uploads/images/') . $this->logo;

        if (!is_file($path)) {
            return '';
        } else {
            return $relative ? (\Yii::getAlias('@uploads/images-rel/') . $this->logo) : $path;
        }
    }

    /**
     * Return user logo url
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getLogoUrl()
    {
        $path = \Yii::getAlias('@uploads/images/') . $this->logo;

        if (!is_file($path)) {
            return '';
        } else {
            return Url::to(\Yii::getAlias('@uploads/images-rel/' . $this->logo), true);
        }
    }

    /**
     * Remove old user logo
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function removeOldLogo()
    {
        $fullPath = \Yii::getAlias('@uploads/images/') . $this->oldLogo;

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    /**
     * Get full name (magic method)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param bool $withEmail
     * @return string
     */
    public function getFullName($withEmail = false)
    {
        return $this->firstName . ' ' . $this->lastName . ($withEmail ? ' [' . $this->email . ']' : '');
    }

    /**
     * Get possible user types
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     * @return array
     */
    public static function getTypes($exclude = [])
    {
        $types = [
            self::TYPE_USER => Yii::t('app', 'User (Buyer)'),
            self::TYPE_MANAGER => Yii::t('app', 'Manager (Administered)'),
            self::TYPE_ADMIN => Yii::t('app', 'Admin AJA'),
            self::TYPE_SUPERADMIN => Yii::t('app', 'Superadmin'),
        ];

        return array_diff_key($types, array_flip($exclude));
    }

    /**
     * Return type caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $type string
     *
     * @return string
     */
    public static function getTypeCaption($type)
    {
        $types = self::getTypes();

        return $types[$type];
    }

    /**
     * Get "user" types
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return array
     */
    public static function getUserTypes()
    {
        return [User::TYPE_USER, User::TYPE_MANAGER];
    }

    /**
     * Get "admin" types
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return array
     */
    public static function getAdminTypes()
    {
        return [User::TYPE_ADMIN, User::TYPE_SUPERADMIN];
    }

    /**
     * Check if user is admin
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return bool
     */
    public function isAdmin()
    {
        return in_array($this->type, self::getAdminTypes());
    }

    /**
     * Check if user is superadmin
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return bool
     */
    public function isSuperadmin()
    {
        return $this->type == self::TYPE_SUPERADMIN;
    }

    public function isManager()
    {
        return $this->type == self::TYPE_MANAGER;
    }

    public function isBuyer()
    {
        return $this->type == self::TYPE_USER;
    }

    /**
     * Check if allowed to remove user
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return bool
     */
    public function isAllowDelete()
    {
        return !$this->isSuperadmin() && $this->id != Yii::$app->user->id;
    }

    /**
     * Check if allowed to remove user
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return bool
     */
    public function isAllowUpdate()
    {
        return ($this->isSuperadmin() && Yii::$app->user->identity->isSuperadmin()) || !$this->isSuperadmin();
    }

    /**
     * Get possible professions
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @param bool $addAdminProfessions
     * @return array
     */
    public static function getProfessions($exclude = [], $addAdminProfessions = false)
    {
        $list = (new Newsletter())->professionList($addAdminProfessions);

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return profession caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getProfessionCaption($value)
    {
        $list = self::getProfessions([], true);

        return isset($list[$value]) ? $list[$value] : null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserHistories()
    {
        return $this->hasMany(UserHistory::className(), ['userID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccessRequests()
    {
        return $this->hasMany(RoomAccessRequest::className(), ['userID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomAccessRequest()
    {
        return $this->hasOne(RoomAccessRequest::className(), ['userID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProposals()
    {
        return $this->hasMany(Proposal::className(), ['userID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfileCompany()
    {
        return $this->hasOne(ProfileCompany::className(), ['userID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfileRealEstate()
    {
        return $this->hasOne(ProfileRealEstate::className(), ['userID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfileCoownership()
    {
        return $this->hasOne(ProfileCoownership::className(), ['userID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfileCV()
    {
        return $this->hasOne(ProfileCV::className(), ['userID' => 'id']);
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * Trying to get access request for CV room
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param Room $room
     * @return bool
     */
    public function getCVAccessRequest(Room $room)
    {
        if ($this->isBuyer() && $room->section == DataroomModule::SECTION_CV && $room->publishedOrExpired()) {

            if ($this->hasValidatedAccessRequest($room)) {
                return true;
            }

            if ($validatedAccessRequest = $this->getAnyValidatedCVAccessRequest()) {
                return $this->createCopyCVAccessRequest($validatedAccessRequest, $room);
            }
        }

        return false;
    }

    /**
     * Create copy of CV access request (to get request for another room)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param RoomAccessRequest $accessRequest
     * @param Room $room
     * @return mixed
     */
    private function createCopyCVAccessRequest(RoomAccessRequest $accessRequest, Room $room)
    {
        if ($pendingAccessRequest = $this->getPendingAccessRequest($room)) {
            $pendingAccessRequest->delete();
        }

        $clonedAccessRequest = clone $accessRequest;
        $clonedAccessRequest->isNewRecord = true;
        $clonedAccessRequest->id = null;
        $clonedAccessRequest->roomID = $room->id;
        $clonedAccessRequest->createdDate = date('Y-m-d H:i:s');
        $clonedAccessRequest->updatedDate = date('Y-m-d H:i:s');
        $clonedAccessRequest->save(false);

        $clonedAccessRequestCV = clone $accessRequest->roomAccessRequestCV;
        $clonedAccessRequestCV->isNewRecord = true;
        $clonedAccessRequestCV->accessRequestID = $clonedAccessRequest->id;

        return $clonedAccessRequestCV->save(false);
    }


    /**
     * Trying to get access request for all CV rooms
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function getCVAccessRequestAllRooms()
    {
        if ($validatedAccessRequest = $this->getAnyValidatedCVAccessRequest()) {

            $validatedAccessRoomIDs = RoomAccessRequest::find()
                ->select('roomID')
                ->innerJoinWith(['room' => function (ActiveQuery $query) {
                    $query->andOnCondition(['Room.section' => DataroomModule::SECTION_CV]);
                }])
                ->andWhere(['RoomAccessRequest.userID' => $this->id])
                ->andWhere(['IS NOT', 'RoomAccessRequest.validatedBy', null])
                ->column();

            $roomCVList = RoomCV::find()
                ->joinWith(['room' => function (RoomQuery $q) use ($validatedAccessRoomIDs) {
                    $q->published()->andWhere(['NOT IN', 'Room.id', $validatedAccessRoomIDs]);
                }])
                ->all();

            foreach ($roomCVList as $roomCV) {
                $this->createCopyCVAccessRequest($validatedAccessRequest, $roomCV->room);
            }
        }
    }

    /**
     * @param  Room $room
     * @return boolean
     */
    public function getPendingAccessRequest(Room $room)
    {
        if (!$this->isBuyer()) {
            return false;
        }

        return $this->getAccessRequests()
            ->andWhere(['RoomAccessRequest.roomID' => $room->id])
            ->andWhere(['RoomAccessRequest.validatedBy' => null])
            ->one();
    }

    /**
     * @param  Room    $room
     * @return boolean
     */
    public function hasPendingAccessRequest(Room $room)
    {
        if (!$this->isBuyer()) {
            return false;
        }

        return $this->getAccessRequests()
            ->andWhere(['RoomAccessRequest.roomID' => $room->id])
            ->andWhere(['RoomAccessRequest.status' => RoomAccessRequest::STATUS_WAITING])
            ->exists();
    }

    /**
     * @param  Room    $room
     * @return boolean
     */
    public function hasValidatedAccessRequest(Room $room)
    {
        if (!$this->isBuyer()) {
            return false;
        }

        return $this->getAccessRequests()
            ->andWhere(['RoomAccessRequest.roomID' => $room->id])
            ->andWhere(['RoomAccessRequest.status' => RoomAccessRequest::STATUS_ACCEPTED])
            ->exists();
    }

    /**
     * @param  Room    $room
     * @return boolean
     */
    public function hasRefusedAccessRequest(Room $room)
    {
        if (!$this->isBuyer()) {
            return false;
        }

        return $this->getAccessRequests()
            ->andWhere(['RoomAccessRequest.roomID' => $room->id])
            ->andWhere(['RoomAccessRequest.status' => RoomAccessRequest::STATUS_REFUSED])
            ->exists();
    }

    /**
     * Check if user has at least one access request for specified section
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $userID
     * @param string $section
     * @param bool $onlyValidated
     * @return bool
     */
    public static function hasAnyAccessRequest($userID, $section, $onlyValidated = false)
    {
        $query = User::find();

        $query->innerJoinWith(['accessRequests' => function (ActiveQuery $query) use ($section) {
            if ($section == DataroomModule::SECTION_COMPANIES) {
                $query->innerJoinWith('roomAccessRequestCompany');
            } elseif ($section == DataroomModule::SECTION_REAL_ESTATE) {
                $query->innerJoinWith('roomAccessRequestRealEstate');
            } elseif ($section == DataroomModule::SECTION_COOWNERSHIP) {
                $query->innerJoinWith('roomAccessRequestCoownership');
            } elseif ($section == DataroomModule::SECTION_CV) {
                $query->innerJoinWith('roomAccessRequestCV');
            }
        }]);
        $query->andWhere(['RoomAccessRequest.userID' => $userID]);

        if ($onlyValidated) {
            $query->andWhere(['IS NOT', 'RoomAccessRequest.validatedBy', null]);
        }

        return $query->exists();
    }

    /**
     * Get any validated CV access request
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return RoomAccessRequest
     */
    public function getAnyValidatedCVAccessRequest()
    {
        if (!$this->isBuyer()) {
            return false;
        }

        return RoomAccessRequest::find()
            ->innerJoinWith(['room' => function (ActiveQuery $query) {
                $query->andOnCondition(['Room.section' => DataroomModule::SECTION_CV]);
            }])
            ->andWhere(['RoomAccessRequest.userID' => $this->id])
            ->andWhere(['IS NOT', 'RoomAccessRequest.validatedBy', null])
            ->one();
    }

    /**
     * @param  Room    $room
     * @return boolean
     */
    public function hasProposal(Room $room)
    {
        if (!$this->isBuyer()) {
            return false;
        }

        return $this->getProposals()
            ->andWhere(['Proposal.roomID' => $room->id])
            ->exists();
    }

    /**
     * @return string
     */
    public function getFirstLoginTime()
    {
        $history = $this->getUserHistories()
            ->andWhere(['event' => UserHistory::EVENT_LOGIN])
            ->orderBy('UserHistory.createdDate ASC')
            ->one();

        return $history ? Carbon::parse($history->createdDate)->format('d/m/Y H:i') : null;
    }

    /**
     * @return string
     */
    public function getLastLoginTime()
    {
        $history = $this->getUserHistories()
            ->andWhere(['event' => UserHistory::EVENT_LOGIN])
            ->orderBy('UserHistory.createdDate DESC')
            ->one();

        return $history ? Carbon::parse($history->createdDate)->format('d/m/Y H:i') : null;
    }

    /**
     * Checks whether a user can receive emailing (is visible in AJA list module).
     * 
     * @return bool
     */
    public function canReceiveMailing()
    {
        return $this->isActive && $this->isMailingContact;
    }

    /**
     * Get link to unsubscribe from any mailing
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getUnsubscribeLink()
    {
        return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/newsletter/user/mailing-unsubscribe']);
    }

    /**
     * Unsubscribe from newsletters
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return bool
     */
    public function mailingUnsubscribe()
    {
        $this->isMailingContact = 0;

        return $this->save(false, ['isMailingContact']);
    }

    /**
     * Generate deleted email
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $postfix
     */
    public function generateDeletedEmail($postfix = '_was')
    {
        $this->email .= $postfix;

        if (self::findOne(['email' => $this->email])) {
            $this->generateDeletedEmail();
        }
    }
}
