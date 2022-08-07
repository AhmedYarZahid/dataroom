<?php

namespace backend\modules\contact\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use backend\modules\document\models\Document;

/**
 * This is the model class for table "Contact".
 *
 * @property integer $id
 * @property string $type
 * @property string $firstName
 * @property string $lastName
 * @property string $email
 * @property string $phone
 * @property string $company
 * @property string $subject
 * @property string $body
 * @property string $code
 * @property integer $toUserID
 * @property integer $fromUserID
 * @property integer $responsesNumber
 * @property integer $isClosed
 * @property string $createdDate
 *
 * @property ContactThread[] $contactThreads
 */
class Contact extends ActiveRecord
{
    const DUMMY_GUEST = -1;
    const DUMMY_ADMIN = -2;

    const TYPE_USUAL = 'usual';
    const TYPE_RESUME = 'resume';
    const TYPE_PROJECT = 'project';

    /**
     * @var bool whether contact has new message from user
     */
    public $hasNewMessage;

    /**
     * @var string Verification code to be enetered by guest
     */
    public $verifyCode;

    public $resume;
    public $coverLetter;
    public $attachment;

    public $subscribe;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Contact';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstName', 'lastName', 'email', 'subject', 'body'], 'required'],
            [['phone'], 'required', 'on' => 'contact-resume'],
            [['company'], 'required', 'on' => ['contact-project', 'contact-us']],

            [['body'], 'string'],
            [['toUserID', 'fromUserID', 'responsesNumber', 'isClosed'], 'integer'],
            [['createdDate'], 'safe'],
            [['firstName', 'lastName'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 150],
            ['email', 'email'],
            [['phone'], 'string', 'max' => 10],
            [['subject', 'mandate'], 'string', 'max' => 255],
            [['company'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 20],
            ['civility', 'in', 'range' => ['sir', 'madam', 'master']],

            ['attachment', 'file', 'extensions' => ['pdf','doc','docx', 'txt', 'jpg','jpeg','gif','png'], 'when' => function (Contact $model) {
                return $model->type == Contact::TYPE_USUAL;
            }],
            [['resume'], 'file', 'extensions' => ['pdf','doc','docx', 'txt', 'jpg','jpeg','gif','png'], 'skipOnEmpty' => false, 'when' => function (Contact $model) {
                return $model->type == Contact::TYPE_RESUME;
            }],
            [['coverLetter'], 'file', 'extensions' => ['pdf','doc','docx', 'txt', 'jpg','jpeg','gif','png'], 'skipOnEmpty' => true, 'when' => function (Contact $model) {
                return $model->type == Contact::TYPE_RESUME;
            }],

            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha', 'when' => function ($model) {return Yii::$app->user->isGuest && empty(Yii::$app->params['disableCaptcha']);}],

            ['subscribe', 'boolean', 'when' => function(Contact $model) {
                return $model->type == Contact::TYPE_USUAL;
            }],
            ['civility', 'required', 'when' => function(Contact $model) {
                return $model->type == Contact::TYPE_USUAL;
            }],
        ];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->attachment = UploadedFile::getInstance($this, 'attachment');
            $this->resume = UploadedFile::getInstance($this, 'resume');
            $this->coverLetter = UploadedFile::getInstance($this, 'coverLetter');

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('contact', 'ID'),
            'firstName' => Yii::t('contact', 'First Name'),
            'lastName' => Yii::t('contact', 'Last Name'),
            'email' => Yii::t('contact', 'Email'),
            'phone' => Yii::t('contact', 'Phone'),
            'company' => Yii::t('contact', 'Company'),
            'subject' => Yii::t('contact', 'Subject'),
            'body' => Yii::t('contact', 'Body'),
            'code' => Yii::t('contact', 'Code'),
            'toUserID' => Yii::t('contact', 'To User ID'),
            'fromUserID' => Yii::t('contact', 'From User ID'),
            'responsesNumber' => Yii::t('contact', 'Responses'),
            'isClosed' => Yii::t('contact', 'Closed'),
            'createdDate' => Yii::t('contact', 'Created Date'),
            'type' => Yii::t('contact', 'Type'),
            'resume' => Yii::t('contact', 'Resume'),
            'coverLetter' => Yii::t('contact', 'Cover Letter'),
            'verifyCode' => Yii::t('contact', 'Verify Code'),
            'mandate' => Yii::t('contact', 'Name of the mandate'),
            'civility' => Yii::t('contact', 'Civility'),
            'attachment' => Yii::t('contact', 'Attachment'),
            'subscribe' => Yii::t('contact', 'Subscribe to news'),
        ];
    }

    /**
     * Get possible types
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     * @return array
     */
    public static function getTypes($exclude = [])
    {
        $result = [
            self::TYPE_USUAL => Yii::t('app', 'Contact'),
            self::TYPE_RESUME => Yii::t('app', 'Resume'),
            self::TYPE_PROJECT => Yii::t('app', 'Project'),
        ];

        return array_diff_key($result, array_flip($exclude));
    }

    /**
     * Return type caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getTypeCaption($value)
    {
        $list = self::getTypes();

        return isset($list[$value]) ? $list[$value] : null;
    }

    public function getCivilities()
    {
        return [
            'sir' => Yii::t('contact', 'Sir'),
            'madam' => Yii::t('contact', 'Madam'),
            'master' => Yii::t('contact', 'Master'),
        ];
    }

    public function getCivilityCaption()
    {
        $list = $this->getCivilities();

        return $this->civility && isset($list[$this->civility]) ? $list[$this->civility] : null;
    }

    /**
     * Generates code for thread recognition and security
     *
     * @author Perica Levatic <perica.levatic@gmail.com>
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function generateCode()
    {
        $this->code = Yii::$app->security->generateRandomString(20);
    }

    public function afterDelete()
    {
        parent::afterDelete();

        foreach ($this->documents as $document) {
            $deleted = $document->delete();
            if ($deleted) {
                $document->setOldAttribute('filePath', $document->filePath);
                $document->removeOldDocument();
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContactThreads()
    {
        return $this->hasMany(ContactThread::className(), ['contactID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastContactThreadMessage()
    {
        return $this->hasOne(ContactThread::className(), ['contactID' => 'id'])->onCondition(['isLastMessage' => 1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments()
    {
        return $this->hasMany(Document::className(), ['contactID' => 'id']);
    }
}
