<?php

namespace backend\modules\mailing\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\User;

/**
 * This is the model class for table "MailingList".
 *
 * @property integer $id
 * @property string $name
 * @property integer $createdByUserID
 * @property string $createdDate
 * @property string $updatedDate
 *
 * @property Newsletter $newsletter
 * @property User $createdByUser
 */
class MailingList extends \yii\db\ActiveRecord
{
    public $contactIds;
    public $extraContactIds;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'MailingList';
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
            ['extraContactIds','safe'],
            [['name', 'createdByUserID'], 'required'],
            [['createdByUserID'], 'integer'],
            [['createdDate', 'updatedDate'], 'safe'],
            [['name'], 'string', 'max' => 45],
            [['createdByUserID'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['createdByUserID' => 'id']],
            ['contactIds', 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('admin', 'Name'),
            'createdByUserID' => Yii::t('admin', 'Created by'),
            'createdDate' => Yii::t('admin', 'Created Date'),
            'updatedDate' => Yii::t('admin', 'Updated Date'),
            'contactIds' => Yii::t('admin', 'Users'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedByUser()
    {
        return $this->hasOne(User::className(), ['id' => 'createdByUserID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContacts()
    {
        return $this->hasMany(MailingContact::className(), ['listID' => 'id']);
    }

    /**
     * Loads contacts to use them in select list.
     * 
     * @return array Array of contact ids in format user_<ID> or newsletter_<ID>
     */
    public function loadContacts()
    {
        $this->contactIds = [];

        foreach ($this->contacts as $contact) {
            if ($contact->userID) {
                $this->contactIds[] = 'user_' . $contact->userID;
            } elseif ($contact->newsletterID) {
                $this->contactIds[] = 'newsletter_' . $contact->newsletterID;
            }
        }

        return $this->contactIds;
    }

    /**
     * Returns array of recipients for yii2 mailer.
     * 
     * @return array Array of emails => names
     */
    public function getRecipients($withVariables = false)
    {
        $contacts = $this->getContacts()->with(['user', 'newsletter'])->all();

        $to = [];
        
        foreach ($contacts as $contact) {
            if ($contact->user && $contact->user->canReceiveMailing()) {
                $to[$contact->user->email] = $withVariables
                    ? [
                        'unsubscribeLink' => $contact->getUnsubscribeLink()
                    ]
                    : $contact->user->fullName;
            } else if ($contact->newsletter && $contact->newsletter->canReceiveMailing()) {
                $to[$contact->newsletter->email] = $withVariables
                    ? [
                        'unsubscribeLink' => $contact->getUnsubscribeLink()
                    ]
                    : $contact->newsletter->fullName;
            }
        }

        return $to;
    }
}