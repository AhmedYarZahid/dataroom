<?php

namespace backend\modules\mailing\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\behaviors\TimestampBehavior;
use common\models\User;
use common\models\Newsletter;

/**
 * This is the model class for table "MailingContact".
 *
 * @property integer $id
 * @property integer $listID
 * @property integer $userID
 * @property integer $newsletterID
 * @property string $code
 * @property string $createdDate
 *
 * @property MailingList $list
 * @property User $user
 * @property Newsletter $newsletter
 */
class MailingContact extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'MailingContact';
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
            [['listID'], 'required'],
            [['listID', 'userID', 'newsletterID'], 'integer'],
            [['createdDate'], 'safe'],
            [['code'], 'string', 'length' => 32],
            [['listID'], 'exist', 'skipOnError' => true, 'targetClass' => MailingList::className(), 'targetAttribute' => ['listID' => 'id']],
            [['userID'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userID' => 'id']],
            [['newsletterID'], 'exist', 'skipOnError' => true, 'targetClass' => Newsletter::className(), 'targetAttribute' => ['newsletterID' => 'id']],

            [['fullName'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'listID' => 'List ID',
            'userID' => 'User ID',
            'newsletterID' => 'Newsletter ID',
            'createdDate' => 'Created Date',
            'type' => Yii::t('admin', 'Mailing contact type'),
        ];
    }

    /**
     * Get unsubscribe link
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return mixed
     */
    public function getUnsubscribeLink()
    {
        return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/newsletter/unsubscribe', 'code' => $this->code]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getList()
    {
        return $this->hasOne(MailingList::className(), ['id' => 'listID']);
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
    public function getNewsletter()
    {
        return $this->hasOne(Newsletter::className(), ['id' => 'newsletterID']);
    }

    public function getType()
    {
        if ($this->user) {
            return $this->user->getTypeCaption($this->user->type);
        } elseif ($this->newsletter) {
            return 'Contact';
        }
    }
}