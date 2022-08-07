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
 * This is the model class for table "NotifySendList".
 *
 * The followings are the available columns in table 'NotifySendList':
 * @property integer $id
 * @property integer $notifyID
 * @property integer $eventID
 * @property string $email
 * @property integer $userID
 * @property string $subject
 * @property string $body
 * @property string $priority
 * @property string $attachedFiles
 * @property integer $failedAttemptsCount
 * @property string $createdDate
 *
 * The followings are the available model relations:
 * @property Notify $notify
 * @property User $user
 */
class NotifySendList extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'NotifySendList';
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
			[['notifyID', 'email', 'subject', 'body', 'createdDate'], 'required'],
			[['eventID', 'notifyID', 'userID', 'failedAttemptsCount'], 'integer'],
			[['email'], 'string', 'max' => 150],
			[['subject'], 'string', 'max' => 100],
			[['attachedFiles'], 'string', 'max' => 500],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'notifyID' => 'Notify',
			'email' => 'Email',
			'userID' => 'User',
			'subject' => 'Subject',
			'body' => 'Body',
			'attachedFiles' => 'Attached Files',
			'failedAttemptsCount' => 'Failed Attempts Count',
			'createdDate' => 'Created Date',
		];
	}

    /**
     * Put record to notify send list
     *
     * @param integer $notifyID
     * @param integer $eventID
     * @param string|array $email
     * @param integer $userID
     * @param string $subject
     * @param string $body
     * @param int $priority
     * @param array $attach     array(filePath => fileName)
     * @return boolean
     */
    public static function addRecord($notifyID, $eventID, $email, $userID, $subject, $body, $priority = 255, $attach = array())
    {
        $model = new NotifySendList();

        $model->notifyID = $notifyID;
        $model->eventID = $eventID;
        $model->email   = is_array($email) ? join(';', $email) : $email;
        $model->userID  = $userID > 0 ? $userID : new Expression('NULL');
        $model->subject = $subject;
        $model->body    = $body;
        $model->priority = $priority;
        $model->attachedFiles = serialize($attach);
        $model->createdDate = date('Y-m-d H:i:s');

        return $model->save(false);
    }

    /**
     * Returns notifies list for sending
     *
     * @param   integer $limit
     * @return  object NotifySendList
     */
    public static function getListToSend($limit)
    {
        return NotifySendList::find()->limit($limit)->orderBy('priority ASC, createdDate ASC')->all();
    }

    /**
     * Increase notify failed attempts count
     *
     * @param   integer $id
     * @return  boolean
     */
    public static function increaseFailedAttemptsCount($id)
    {
		return NotifySendList::updateAllCounters(
			['failedAttemptsCount' => 1],
			'id = ' . intval($id)
		);
    }

    /**
     * Delete notify from sending list
     *
     * @param   integer $id
     * @return  boolean
     */
    public static function deleteRecord($id)
    {
		return NotifySendList::deleteAll(
			'id = ' . intval($id)
		);
    }
}