<?php

namespace backend\modules\dataroom\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\User;

/**
 * This is the model class for table "Proposal".
 *
 * @property integer $id
 * @property integer $roomID
 * @property integer $creatorID
 * @property integer $userID
 * @property string $createdDate
 *
 * @property Room $room
 * @property User $user
 */
class Proposal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Proposal';
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
    public function rules()
    {
        return [
            [['roomID', 'userID'], 'required'],
            [['roomID', 'userID', 'creatorID'], 'integer'],
            [['createdDate'], 'safe'],
            [['roomID'], 'exist', 'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['roomID' => 'id']],
            [['userID'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userID' => 'id']],
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
            'creatorID' => 'Creator ID',
            'createdDate' => 'Created Date',
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
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creatorID']);
    }
}