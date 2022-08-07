<?php

namespace backend\modules\contact\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ContactThread".
 *
 * @property integer $id
 * @property integer $contactID
 * @property string $sender
 * @property string $body
 * @property integer $isLastMessage
 * @property string $createdDate
 *
 * @property Contact $contact
 */
class ContactThread extends ActiveRecord
{
    const SENDER_USER = 'user';
    const SENDER_ADMIN = 'admin';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ContactThread';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contactID', 'body'], 'required'],
            [['contactID', 'isLastMessage'], 'integer'],
            [['sender', 'body'], 'string'],
            [['createdDate'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('contact', 'ID'),
            'contactID' => Yii::t('contact', 'Contact ID'),
            'sender' => Yii::t('contact', 'Sender'),
            'body' => Yii::t('contact', 'Body'),
            'isLastMessage' => Yii::t('contact', 'Is Last Message'),
            'createdDate' => Yii::t('contact', 'Created Date'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            self::updateAll(['isLastMessage' => 0], 'contactID = ' . $this->contactID . ' AND isLastMessage = 1 AND id <> ' . $this->id);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(Contact::className(), ['id' => 'contactID']);
    }
}
