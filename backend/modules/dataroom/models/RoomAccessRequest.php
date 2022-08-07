<?php

namespace backend\modules\dataroom\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\User;

/**
 * This is the model class for table "RoomAccessRequest".
 *
 * @property integer $id
 * @property integer $roomID
 * @property integer $userID
 * @property string $status
 * @property integer $validatedBy
 * @property integer $refusedBy
 * @property string $createdDate
 * @property string $updatedDate
 *
 * @property Room $room
 * @property User $user
 * @property User $validatedBy0
 */
class RoomAccessRequest extends \yii\db\ActiveRecord
{
    const STATUS_WAITING = 'waiting';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REFUSED = 'refused';

    public $userCompany;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'RoomAccessRequest';
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['roomID', 'userID'], 'required'],
            [['roomID', 'userID', 'validatedBy', 'refusedBy'], 'integer'],
            [['createdDate', 'updatedDate'], 'safe'],
            [['status'], 'string'],
            [['roomID'], 'exist', 'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['roomID' => 'id']],
            [['userID'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userID' => 'id']],
            [['validatedBy'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['validatedBy' => 'id']],
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
            'status' => 'Status',
            'validatedBy' => 'Validated By',
            'refusedBy' => 'Refused By',
            'createdDate' => 'Created Date',
            'updatedDate' => 'Updated Date',
        ];
    }

    /**
     * Get possible statuses
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getStatuses($exclude = [])
    {
        $list = [
            self::STATUS_WAITING => Yii::t('app', 'Waiting'),
            self::STATUS_ACCEPTED => Yii::t('app', 'Accepted'),
            self::STATUS_REFUSED => Yii::t('app', 'Refused'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return status caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getStatusCaption($value)
    {
        $list = self::getStatuses();

        return isset($list[$value]) ? $list[$value] : null;
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
    public function getRoomAccessRequestCompany()
    {
        return $this->hasOne(RoomAccessRequestCompany::className(), ['accessRequestID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomAccessRequestRealEstate()
    {
        return $this->hasOne(RoomAccessRequestRealEstate::className(), ['accessRequestID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomAccessRequestCoownership()
    {
        return $this->hasOne(RoomAccessRequestCoownership::className(), ['accessRequestID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomAccessRequestCV()
    {
        return $this->hasOne(RoomAccessRequestCV::className(), ['accessRequestID' => 'id']);
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
        return $this->hasOne(User::className(), ['id' => 'validatedBy']);
    }
}