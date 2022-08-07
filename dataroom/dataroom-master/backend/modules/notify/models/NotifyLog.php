<?php

namespace backend\modules\notify\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use \yii\db\Connection;
use common\models\User;
use yii\db\Expression;
use yii\helpers\Url;
use yii\base\Exception;
use yii\helpers\Html;

/**
 * This is the model class for table "NotifyLog".
 *
 * The followings are the available columns in table 'NotifyLog':
 * @property integer $id
 * @property integer $notifyID
 * @property integer $eventID
 * @property string $email
 * @property integer $userID
 * @property string $status
 * @property string $errorMessage
 * @property string $createdDate
 *
 * The followings are the available model relations:
 * @property Notify $notify
 */
class NotifyLog extends ActiveRecord
{
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'NotifyLog';
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
				'value' => new Expression('NOW()'),
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['eventID', 'email', 'status', 'createdDate'], 'required'],
			[['eventID', 'notifyID', 'userID'], 'integer'],

			[['email'], 'string', 'max' => 150],
			[['status'], 'string', 'max' => 7],
			[['errorMessage'], 'string', 'max' => 300],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'notifyID' => 'Notify',
			'eventID' => 'Event',
			'email' => 'Email',
			'userID' => 'User',
			'status' => 'Status',
			'errorMessage' => 'Error Message',
			'createdDate' => 'Created Date',
		];
	}

    /**
     * Put record to notify log
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param   integer|null $notifyID
     * @param   integer $eventID
     * @param   string  $email
     * @param   integer $userID
     * @param   integer $status
     * @param   string  $errorMessage
     *
     * @return  boolean
     */
    public static function addRecord($notifyID, $eventID, $email, $userID, $status, $errorMessage = '')
    {
        $model = new NotifyLog();

        $model->notifyID = $notifyID;
        $model->eventID = $eventID;
		$model->email = is_array($email) ? join(';', $email) : $email;
        $model->userID = $userID;
        $model->status = $status;
        $model->errorMessage = $errorMessage;
        $model->createdDate = date('Y-m-d H:i:s');

        return $model->save(false);
    }
}