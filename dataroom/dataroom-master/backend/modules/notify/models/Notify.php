<?php

namespace backend\modules\notify\models;


use common\helpers\ArrayHelper;
use frontend\models\ContactForm;
use omgdef\multilingual\MultilingualBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use \yii\db\Connection;
use common\models\User;
use yii\db\Expression;
use yii\helpers\Url;
use yii\base\Exception;
use yii\helpers\Html;
use common\helpers\DateHelper;
use frontend\models\AskSignupForm;
use backend\modules\contact\models\Contact;
use backend\modules\contact\models\ContactThread;
use common\models\Request;
use yii\web\UploadedFile;
use lateos\formpage\models\FormPage;
use lateos\formpage\models\FormPageResult;
use backend\modules\notify\models\traits\DataroomTrait;


/**
 * This is the model class for table "Notify".
 *
 * The followings are the available columns in table 'Notify':
 * @property integer $id
 * @property integer $eventID
 * @property string $title
 * @property string $subject
 * @property string $body
 * @property integer $isDefault
 * @property integer $priority
 * @property integer $putToQueue
 * @property string $updatedDate
 *
 * The followings are the available model relations:
 * @property NotifyLog[] $notifyLogs
 * @property NotifySendList[] $notifySendLists
 */
class Notify extends ActiveRecord
{
    use DataroomTrait;

    const EVENT_REGISTRATION = 1;
    const EVENT_REGISTRATION_BY_ADMIN = 2;
    const EVENT_RESET_PASSWORD = 3;
    const EVENT_CHANGE_EMAIL = 4;
    const EVENT_CONTACT_US = 5;
    const EVENT_CONTACT_US_COPY_TO_USER = 6;
    const EVENT_CONTACT_US_USER_REPLY = 7;
    const EVENT_CONTACT_US_ADMIN_REPLY = 8;
    const EVENT_NEW_COMMENT_ADMIN = 9;
    const EVENT_CONTACT_US_RESUME = 10;
    const EVENT_CONTACT_US_RESUME_COPY_TO_USER = 11;

    const EVENT_REGISTRATION_TO_AJA = 12;
    const EVENT_NEW_ACCESS_REQUEST = 13;
    const EVENT_ACCESS_REQUEST_VALIDATED = 14;
    const EVENT_NEW_ROOM_CREATED = 15;
    const EVENT_MANAGER_REGISTRATION = 16;
    const EVENT_NEW_PROPOSAL = 17;
    const EVENT_NEW_PROPOSAL_TO_AJA = 18;
    const EVENT_ROOM_PUBLICATION = 19;
    const EVENT_ROOM_EXPIRATION = 20;
    const EVENT_ROOM_EXPIRED = 21;
    const EVENT_ROOM_ARCHIVED = 22;
    const EVENT_ROOM_UPDATED_TO_BUYERS = 23;
    const EVENT_ROOM_UDPATED_TO_AJA = 24;
    const EVENT_ROOM_HEARING = 25;

    const EVENT_ROOM_CV_UPLOADED = 26;
    const EVENT_ROOM_CV_NEED_TO_CORRECT = 27;

    const EVENT_ACCESS_REQUEST_REFUSED = 28;

    const EVENT_DOCUMENT_ADDED_TO_ROOM = 29;

    // --- Paths to images in email template (for constructing correct url to images) --- //
    private static $imagesPaths = array('/images/email/', '/uploads/editor/');

    public static $eventCaptions = array(
        // User registration
        self::EVENT_REGISTRATION => array(
            'caption' => 'Registration notification (to buyer)',
            'tags' => array(
                '{FIRST_NAME}' => "First Name",
                '{LAST_NAME}' => "Last Name",
                '{EMAIL}' => "Email",
                '{PASSWORD}' => "Password",
                '{USER_CREATION_DATE}' => "User creation date",
                '{LOGIN_LINK}' => "Login link",
            )
        ),
        self::EVENT_REGISTRATION_TO_AJA => array(
            'caption' => 'Registration notification (to AJA)',
            'tags' => array(
                '{FIRST_NAME}' => "First Name",
                '{LAST_NAME}' => "Last Name",
                '{PASSWORD}' => "Password",
                '{USER_CREATION_DATE}' => "User creation date",
                '{LOGIN_LINK}' => "Login link",
                //'{CONFIRMATION_LINK}' => "Confirmation link",
            )
        ),

        // Access to a room
        self::EVENT_NEW_ACCESS_REQUEST => [
            'caption' => 'Request for access to a room (to AJA)',
            'tags' => [
                '{EMAIL}' => 'Email',
                '{FIRST_NAME}' => "First Name",
                '{LAST_NAME}' => "Last Name",
                '{ROOM_ID}' => 'Room ID',
                '{ROOM_TITLE}' => 'Room title',
                '{ROOM_LINK}' => 'Link to the room page',
                '{REQUEST_ID}' => 'Request ID',
                '{REQUEST_LINK}' => 'Link to the request page',
            ],
        ],
        self::EVENT_ACCESS_REQUEST_VALIDATED => [
            'caption' => 'Validation of an application for access to a room (to buyer)',
            'tags' => [
                '{EMAIL}' => 'Email',
                '{FIRST_NAME}' => "First Name",
                '{LAST_NAME}' => "Last Name",
                '{ROOM_ID}' => 'Room ID',
                '{ROOM_TITLE}' => 'Room title',
                '{ROOM_LINK}' => 'Link to the room page',
            ],
        ],
        self::EVENT_ACCESS_REQUEST_REFUSED => [
            'caption' => 'Refuse of an application for access to a room (to buyer)',
            'tags' => [
                '{EMAIL}' => 'Email',
                '{FIRST_NAME}' => "First Name",
                '{LAST_NAME}' => "Last Name",
                '{ROOM_ID}' => 'Room ID',
                '{ROOM_TITLE}' => 'Room title',
                '{ROOM_LINK}' => 'Link to the room page',
            ],
        ],

        // Room creation
        self::EVENT_NEW_ROOM_CREATED => [
            'caption' => 'Creation of room (to administered)',
            'tags' => [
                '{EMAIL}' => 'Email',
                '{FIRST_NAME}' => "First Name",
                '{LAST_NAME}' => "Last Name",
                '{ROOM_ID}' => 'Room ID',
                '{ROOM_TITLE}' => 'Room title',
                '{ROOM_LINK}' => 'Link to the room page',
            ],
        ],
        self::EVENT_MANAGER_REGISTRATION => [
            'caption' => 'Registration notification (to administered)',
            'tags' => array(
                '{EMAIL}' => 'Email',
                '{FIRST_NAME}' => "First Name",
                '{LAST_NAME}' => "Last Name",
                '{ONE_TIME_LOGIN_LINK}' => "One-time login link",
            )
        ],

        // Proposal
        self::EVENT_NEW_PROPOSAL => [
            'caption' => 'Trade-in offer (to buyer)',
            'tags' => [
                '{EMAIL}' => 'Email',
                '{FIRST_NAME}' => "First Name",
                '{LAST_NAME}' => "Last Name",
                '{ROOM_ID}' => 'Room ID',
                '{ROOM_TITLE}' => 'Room title',
                '{ROOM_LINK}' => 'Link to the room page',
            ],
        ],
        self::EVENT_NEW_PROPOSAL_TO_AJA => [
            'caption' => 'Trade-in offer (to AJA)',
            'tags' => [
                '{EMAIL}' => 'Email',
                '{FIRST_NAME}' => "First Name",
                '{LAST_NAME}' => "Last Name",
                '{ROOM_ID}' => 'Room ID',
                '{ROOM_TITLE}' => 'Room title',
                '{ROOM_LINK}' => 'Link to the room page',
                '{PROPOSAL_ID}' => 'Proposal ID',
                '{PROPOSAL_LINK}' => 'Link to the proposal page',
            ],
        ],

        // Room lifecycle
        self::EVENT_ROOM_EXPIRED => [
            'caption' => 'Room has been expired (to AJA)',
            'tags' => [
                '{EMAIL}' => 'Email',
                '{FIRST_NAME}' => "First Name",
                '{LAST_NAME}' => "Last Name",
                '{ROOM_ID}' => 'Room ID',
                '{ROOM_TITLE}' => 'Room title',
                '{ROOM_LINK}' => 'Link to the room page',
            ],
        ],
        self::EVENT_ROOM_ARCHIVED => [
            'caption' => 'Room has been archived (to AJA)',
            'tags' => [
                '{EMAIL}' => 'Email',
                '{FIRST_NAME}' => "First Name",
                '{LAST_NAME}' => "Last Name",
                '{ROOM_ID}' => 'Room ID',
                '{ROOM_TITLE}' => 'Room title',
                '{ROOM_LINK}' => 'Link to the room page',
            ],
        ],
        self::EVENT_ROOM_PUBLICATION => [
            'caption' => 'Room will be published soon (to AJA)',
            'tags' => [
                '{EMAIL}' => 'Email',
                '{FIRST_NAME}' => "First Name",
                '{LAST_NAME}' => "Last Name",
                '{ROOM_ID}' => 'Room ID',
                '{ROOM_TITLE}' => 'Room title',
                '{ROOM_LINK}' => 'Link to the room page',
            ],
        ],
        self::EVENT_ROOM_EXPIRATION => [
            'caption' => 'Room is expiring soon (to AJA)',
            'tags' => [
                '{EMAIL}' => 'Email',
                '{FIRST_NAME}' => "First Name",
                '{LAST_NAME}' => "Last Name",
                '{ROOM_ID}' => 'Room ID',
                '{ROOM_TITLE}' => 'Room title',
                '{ROOM_LINK}' => 'Link to the room page',
            ],
        ],
        self::EVENT_ROOM_HEARING => [
            'caption' => 'The date of the review hearing offers (to buyers)',
            'tags' => [
                '{ROOM_ID}' => 'Room ID',
                '{ROOM_TITLE}' => 'Room title',
                '{ROOM_LINK}' => 'Link to the room page',
            ],
        ],
        self::EVENT_ROOM_UDPATED_TO_AJA => [
            'caption' => 'Room was updated (to AJA)',
            'tags' => [
                '{ROOM_ID}' => 'Room ID',
                '{ROOM_TITLE}' => 'Room title',
                '{ROOM_LINK}' => 'Link to the room page',
            ],
        ],
        self::EVENT_ROOM_UPDATED_TO_BUYERS => [
            'caption' => 'Room was updated (to buyers)',
            'tags' => [
                '{ROOM_ID}' => 'Room ID',
                '{ROOM_TITLE}' => 'Room title',
                '{ROOM_LINK}' => 'Link to the room page',
            ],
        ],
        self::EVENT_ROOM_CV_UPLOADED => [
            'caption' => 'CV was uploaded to Room (to room creator)',
            'tags' => [
                '{ROOM_ID}' => 'Room ID',
                '{ROOM_TITLE}' => 'Room title',
                '{ROOM_LINK}' => 'Link to the room page',
            ],
        ],
        self::EVENT_ROOM_CV_NEED_TO_CORRECT => [
            'caption' => 'Need to correct CV (to manager)',
            'tags' => [
                '{ROOM_ID}' => 'Room ID',
                '{ROOM_TITLE}' => 'Room title',
                '{ROOM_LINK}' => 'Link to the room page',
            ],
        ],
        self::EVENT_REGISTRATION_BY_ADMIN => array(
            'caption' => 'Registration notification (to user created by admin)',
            'tags' => array(
                '{FIRST_NAME}' => "First Name",
                '{LAST_NAME}' => "Last Name",
                '{PASSWORD}' => "Password",
                '{EMAIL}' => "Email",
                '{USER_CREATION_DATE}' => "User creation date",
                '{LOGIN_LINK}' => "Login link",
            )
        ),
        self::EVENT_RESET_PASSWORD => array(
            'caption' => 'Reset password link',
            'tags' => array(
                '{FIRST_NAME}' => "First name",
                '{LAST_NAME}' => "Last name",
                '{RESET_PASSWORD_LINK}' => "Link to reset user password",
            ),
        ),
        self::EVENT_CHANGE_EMAIL => array(
            'caption' => 'Change email',
            'tags' => array(
                '{FULL_NAME}' => 'Full name',
                '{CONFIRMATION_LINK}' => 'Confirm email change link',
            ),
        ),
        self::EVENT_CONTACT_US => array(
            'caption' => 'Contact Us',
            'tags' => array(
                '{CIVILITY}' => 'Civility',
                '{USER_NAME}' => "User name",
                '{USER_EMAIL}' => "User email",
                '{USER_PHONE}' => "User phone",
                '{USER_COMPANY}' => "User company",
                '{MANDATE}' => 'Mandate',
                '{SUBJECT}' => "Subject",
                '{BODY}' => "Body",
                '{DATE}' => "Contact date",
                '{CONTACT_LINK_ADMIN}' => "Link to contact thread for admin",
            ),
        ),
        self::EVENT_CONTACT_US_COPY_TO_USER => array(
            'caption' => 'Contact Us (copy to user)',
            'tags' => array(
                '{CIVILITY}' => 'Civility',
                '{USER_NAME}' => "User name",
                '{USER_EMAIL}' => "User email",
                '{USER_PHONE}' => "User phone",
                '{USER_COMPANY}' => "User company",
                '{MANDATE}' => 'Mandate',
                '{SUBJECT}' => "Subject",
                '{BODY}' => "Body",
                '{DATE}' => "Contact date",
            ),
        ),
        self::EVENT_CONTACT_US_USER_REPLY => array(
            'caption' => 'Contact Us (user reply)',
            'tags' => array(
                '{CIVILITY}' => 'Civility',
                '{USER_NAME}' => "User name",
                '{USER_EMAIL}' => "User email",
                '{USER_PHONE}' => "User phone",
                '{USER_COMPANY}' => "User company",
                '{MANDATE}' => 'Mandate',
                '{SUBJECT}' => "Subject",
                '{BODY}' => "Body",
                '{DATE}' => "Reply date",
                '{DATE_FIRST}' => 'Date of first message in thread',
                '{CONTACT_LINK_ADMIN}' => "Link to contact thread for admin",
            ),
        ),

        self::EVENT_CONTACT_US_ADMIN_REPLY => array(
            'caption' => 'Contact Us (admin reply)',
            'tags' => array(
                '{CIVILITY}' => 'Civility',
                '{USER_NAME}' => "User name",
                '{USER_EMAIL}' => "User email",
                '{USER_PHONE}' => "User phone",
                '{USER_COMPANY}' => "User company",
                '{MANDATE}' => 'Mandate',
                '{SUBJECT}' => "Subject",
                '{BODY}' => "Body",
                '{DATE}' => "Reply date",
                '{DATE_FIRST}' => 'Date of first message in thread',
                '{CONTACT_LINK_USER}' => "Link to contact thread for user",
            ),
        ),
        self::EVENT_NEW_COMMENT_ADMIN => array(
            'caption' => 'New comment admin notification',
            'tags' => array(
                '{PAGE_TITLE}' => "Page title",
                '{AUTHOR_NAME}' => "Author name",
                '{AUTHOR_EMAIL}' => "Author email",
                '{COMMENT}' => "Comment",
                '{DATE}' => "Comment date",
                '{ADMIN_COMMENT_LINK}' => 'Link to comments editor',
            ),
        ),

        self::EVENT_CONTACT_US_RESUME => array(
            'caption' => 'Contact Us (resume)',
            'tags' => array(
                '{USER_NAME}' => "User name",
                '{USER_EMAIL}' => "User email",
                '{USER_PHONE}' => "User phone",
                '{USER_COMPANY}' => "User company",
                '{SUBJECT}' => "Subject",
                '{BODY}' => "Body",
                '{DATE}' => "Contact date",
                '{CONTACT_LINK_ADMIN}' => "Link to contact thread for admin",
            ),
        ),
        self::EVENT_CONTACT_US_RESUME_COPY_TO_USER => array(
            'caption' => 'Contact Us (resume: copy to user)',
            'tags' => array(
                '{USER_NAME}' => "User name",
                '{USER_EMAIL}' => "User email",
                '{USER_PHONE}' => "User phone",
                '{USER_COMPANY}' => "User company",
                '{SUBJECT}' => "Subject",
                '{BODY}' => "Body",
                '{DATE}' => "Contact date",
            ),
        ),
        self::EVENT_DOCUMENT_ADDED_TO_ROOM => [
            'caption' => 'New document was added to the room',
            'tags' => [
                '{USER_NAME}' => 'User full name',
                '{DOCUMENTS_LIST}' => "Documents list",
                '{ROOM_NAME}' => "Room name",
                '{ROOM_LINK}' => 'Link to the room page',
            ],
        ],
    );

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Notify';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => null,
                'updatedAtAttribute' => 'updatedDate',
                'value' => function() {
                    return date('Y-m-d H:i:s');
                }
            ],
            [
                'class' => MultilingualBehavior::className(),
                'languages' => ArrayHelper::map(Yii::$app->params['languagesList'], 'id', 'name'),
                'languageField' => 'languageID',
                'requireTranslations' => true,
                'defaultLanguage' => Yii::$app->params['defaultLanguageID'],
                'langForeignKey' => 'notifyID',
                'tableName' => 'NotifyLang',
                'attributes' => ['title', 'subject', 'body'],

                //'dynamicLangClass' => false,
                //'langClassName' => NewsLang::className(), // or namespace/for/a/class/PostLang
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['eventID', 'title', 'subject', 'body'], 'required'],
            [['body'], 'validateBody'],
            [['eventID', 'isDefault', 'priority', 'putToQueue'], 'integer'],
            [['title'], 'string', 'max' => 70],
            [['subject'], 'string', 'max' => 100],
        ];
    }

    /**
     * Validates template body
     *
     * @param string $attr
     */
    public function validateBody($attr)
    {
        if (trim(strip_tags($this->body)) === '') {
            $this->addError($attr, 'Body cannot be blank.');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'eventID' => Yii::t('notify', 'Event'),
            'title' => Yii::t('notify', 'Title'),
            'subject' => Yii::t('notify', 'Subject'),
            'body' => Yii::t('notify', 'Body'),
            'isDefault' => Yii::t('notify', 'Is Default'),
            'priority' => Yii::t('notify', 'Priority'),
            'putToQueue' => Yii::t('notify', 'Put to Queue'),
            'updatedDate' => Yii::t('notify', 'Updated Date'),

            'title_en' => Yii::t('notify', 'Title'),
            'subject_en' => Yii::t('notify', 'Subject'),
            'body_en' => Yii::t('notify', 'Body'),

            'title_fr' => Yii::t('notify', 'Title'),
            'subject_fr' => Yii::t('notify', 'Subject'),
            'body_fr' => Yii::t('notify', 'Body'),
        ];
    }

    /**
     * @inheritdoc
     * @return NotifyQuery
     */
    public static function find()
    {
        return new NotifyQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotifySendLists()
    {
        return $this->hasMany(NotifySendList::className(), ['notifyID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotifyLogs()
    {
        return $this->hasMany(NotifyLog::className(), ['notifyID' => 'id']);
    }

    /**
     * Return notify template for specified event
     *
     * @param   integer $eventID
     * @return  Notify
     */
    public static function getTemplateForEvent($eventID)
    {
        return Notify::find()->event($eventID)->defaultTemplate()->one();
    }

    /**
     * Get HTML header for email
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public static function getHtmlHeader()
    {
        return "<!DOCTYPE html><html lang='" . Yii::$app->language . "'><head><meta charset='" . Yii::$app->charset . "'/></head><body>";
    }

    /**
     * Get HTML footer for email
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public static function getHtmlFooter()
    {
        return "</body></html>";
    }

    /**
     * Sends notify email to user
     *
     * @param User $userModel User object
     * @param integer $eventID
     * @param array $tags
     * @param array $attach array(filePath => fileName)
     * @param array $extraParams
     *
     * @return boolean
     */
    private static function sendNotifyToUser(User $userModel, $eventID, $tags = array(), $attach = array(), $extraParams = array())
    {
        if (!$notify = self::getTemplateForEvent($eventID)) {
            NotifyLog::addRecord(new Expression("NULL"), $eventID, $userModel->email, $userModel->id, NotifyLog::STATUS_FAILED, 'Template is not accessible');

            return false;
        }

        $subject = self::parseTemplate($notify->subject, $tags);

        // --- Add extra tags --- //
        $tags['{SITE_URL}'] = Url::to('/', 'http');

        $body = self::parseTemplate($notify->body, $tags, true);

        if ($notify->putToQueue || !self::sendEmail($userModel->email, $subject, $body, $notify->id, $eventID, $userModel->id, $attach)) {
            // --- Put record to sending queue --- //
            NotifySendList::addRecord($notify->id, $eventID, $userModel->email, $userModel->id, $subject, $body, $notify->priority, $attach);

            return false;
        }

        return true;
    }

    /**
     * Sends email
     *
     * @param   string $email
     * @param   string $subject
     * @param   string $body
     * @param   integer $notifyID
     * @param   integer $eventID
     * @param   integer $userID
     * @param   array $attach array(filePath => fileName)
     * @return  boolean
     */
    public static function sendEmail($email, $subject, $body, $notifyID, $eventID, $userID = null, $attach = array())
    {
        try {
            /* Uncomment if need to use mail params from db
              $mailParams = Parameter::getByGroup('mail');
              $message->setFrom(array($mailParams['MAIL_ADMIN_EMAIL'] => $mailParams['MAIL_ADMIN_NAME']));
              $message->setSender($mailParams['MAIL_SENDER_EMAIL']);
              $message->setReturnPath($mailParams['MAIL_RETURN_PATH']);
             */

            $adminEmail = Yii::$app->params['mail']['adminEmail'];
            if (is_array(Yii::$app->params['mail']['adminEmail'])) {
                $adminEmail = Yii::$app->params['mail']['adminEmail'][0];
            }

            $mail = Yii::$app->mailer->compose()
                ->setFrom(array($adminEmail => Yii::$app->params['mail']['adminName']))
                ->setTo($email)
                ->setSubject(Yii::$app->env->getEmailSubject($subject))
                ->setHtmlBody(static::getHtmlHeader() . $body . static::getHtmlFooter())
                ->setReplyTo(Yii::$app->params['mail']['replyToEmail']);

            if (!empty($attach)) {
                foreach ($attach as $filePath => $fileName) {
                    if ($fileName) {
                        $mail->attach($filePath, ['fileName' => $fileName]);
                    }
                }
            }

            if (!$mail->send()) {
                throw new \Exception('Cannot send email');
            }

            // --- Log --- //
            NotifyLog::addRecord($notifyID, $eventID, $email, $userID, NotifyLog::STATUS_SUCCESS);

            return true;
        } catch (\Exception $e) {
            // --- Log --- //
            NotifyLog::addRecord($notifyID, $eventID, $email, $userID, NotifyLog::STATUS_FAILED, $e->getMessage());

            return false;
        }
    }

    /**
     * Optimized regular expressions parse method
     *
     * @param string $body
     * @param array $tags
     * @param boolean $addImagesUrl
     * @return string
     */
    public static function parseTemplate($body, $tags, $addImagesUrl = false)
    {
        $result = preg_replace_callback(
            "/{([\w\s\_\-]+)}/",
            function ($matches) use ($tags) {
                return (isset($tags['{' . $matches[1] . '}']) ? $tags['{' . $matches[1] . '}'] : '');
            },
            $body
        );

        if ($addImagesUrl) {
            $siteUrl = Url::to('/', 'http');
            if ($siteUrl[strlen($siteUrl) - 1] == '/') {
                $siteUrl = substr($siteUrl, 0, -1);
            }

            foreach (Notify::$imagesPaths as $path) {
                $imagesPath = preg_quote($path, '#');
                $result = preg_replace('#(src=")(' . $imagesPath . '.+?")#', '$1' . $siteUrl . '$2', $result);
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->isDefault) {
            Notify::updateAll(['isDefault' => 0], 'eventID = ' . $this->eventID . ' AND id != ' . $this->id);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Returns available filters for notification
     *
     * @return array
     */
    public static function getEventFilter()
    {
        $filter = array();
        foreach (self::$eventCaptions as $eventId => $event) {
            $filter[$eventId] = $event['caption'];
        }

        unset($filter[self::EVENT_CHANGE_EMAIL]);

        return $filter;
    }

    /**
     * Get attribute name based on language (multilingual model) that should be used in a form
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $attribute
     * @param string $language
     * @return string
     */
    public function getFormAttributeName($attribute, $language)
    {
        return $language == Yii::$app->params['defaultLanguageID'] ? $attribute : $attribute . "_" . $language;
    }


    // ------------------------------------- //
    // ---- Send notifications methods ----- //
    // ------------------------------------- //


    /**
     * Sends to user registration notification
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param User $userModel
     * @return boolean
     */
    public static function sendSignupNotify(User $userModel)
    {
        $tags = array(
            '{FIRST_NAME}' => Html::encode($userModel->firstName),
            '{LAST_NAME}' => Html::encode($userModel->lastName),
            '{PASSWORD}' => Html::encode($userModel->password),
            '{EMAIL}' => $userModel->email,
            '{USER_CREATION_DATE}' => DateHelper::getFrenchFormatDbDate($userModel->createdDate),
            '{LOGIN_LINK}' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/dataroom/user/login'])
        );

        $sent = self::sendNotifyToUser($userModel, self::EVENT_REGISTRATION, $tags);

        if ($sent) {
            $adminModels = User::find()->active()->ofType(User::TYPE_ADMIN)->all();
            foreach ($adminModels as $admin) {
                $sent = self::sendNotifyToUser($admin, self::EVENT_REGISTRATION_TO_AJA, $tags) && $sent;
            }
        }

        return $sent;
    }

    /**
     * Sends to user notification that account was created by admin
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param User $userModel
     * @return boolean
     */
    public static function sendUserCreatedByAdmin(User $userModel)
    {
        $tags = array(
            '{PASSWORD}' => Html::encode($userModel->password),
            '{FIRST_NAME}' => Html::encode($userModel->firstName),
            '{LAST_NAME}' => Html::encode($userModel->lastName),
            '{EMAIL}' => $userModel->email,
            '{USER_CREATION_DATE}' => DateHelper::getFrenchFormatDbDate($userModel->createdDate),
            '{LOGIN_LINK}' => $userModel->isAdmin()
                ? Yii::$app->urlManagerBackend->createAbsoluteUrl(['/site/login'])
                : Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/dataroom/user/login'])

            //'{CONFIRMATION_LINK}' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['site/email-confirm', 'code' => $userModel->confirmationCode])
        );

        return self::sendNotifyToUser($userModel, static::EVENT_REGISTRATION_BY_ADMIN, $tags);
    }

    /**
     * Sends to user Forgot Password notification
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param User $userModel
     * @return boolean
     */
    public static function sendResetPasswordLink(User $userModel)
    {
        $tags = array(
            '{FIRST_NAME}' => Html::encode($userModel->firstName),
            '{LAST_NAME}' => Html::encode($userModel->lastName),
            '{RESET_PASSWORD_LINK}' => Url::to(['/reset-password', 'token' => $userModel->passwordResetToken], true)
        );

        return self::sendNotifyToUser($userModel, self::EVENT_RESET_PASSWORD, $tags);
    }

    /**
     * Sends to user link to change email
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param User $userModel
     * @return boolean
     */
    public static function sendChangeEmailConfirmationLink(User $userModel)
    {
        $tags = array(
            '{FULL_NAME}' => Html::encode($userModel->fullName),
            '{CONFIRMATION_LINK}' => Yii::$app->urlManager->createAbsoluteUrl(['site/email-change-confirm', 'code' => $userModel->confirmationCode]),
        );

        return self::sendNotifyToUser($userModel, self::EVENT_CHANGE_EMAIL, $tags);
    }

    /**
     * Sends to admin new "Contact Us" notification
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param Contact $model
     * @return boolean
     */
    public static function sendContactUs(Contact $model)
    {
        $adminModel = new User();
        $adminModel->id = -1; // Dummy admin
        $adminModel->email = Yii::$app->params['mail']['contactEmail'];

        switch ($model->type) {
            case Contact::TYPE_RESUME:
                $eventID = self::EVENT_CONTACT_US_RESUME;
                $userCopyEventID = self::EVENT_CONTACT_US_RESUME_COPY_TO_USER;
                break;

            default:
                $eventID = self::EVENT_CONTACT_US;
                $userCopyEventID = self::EVENT_CONTACT_US_COPY_TO_USER;
        }

        $attach = [];
        foreach ($model->documents as $document) {
            $attach[$document->getDocumentPath()] = $document->getDocumentName();
        }

        $tags = array(
            '{CIVILITY}' => Html::encode($model->getCivilityCaption()),
            '{USER_NAME}' => Html::encode($model->firstName . ' ' . $model->lastName),
            '{USER_EMAIL}' => Html::encode($model->email),
            '{USER_PHONE}' => Html::encode($model->phone),
            '{USER_COMPANY}' => Html::encode($model->company),
            '{MANDATE}' => Html::encode($model->mandate),
            '{SUBJECT}' => Html::encode($model->subject),
            '{BODY}' => nl2br(Html::encode($model->body)),
            '{DATE}' => DateHelper::getFrenchFormatDbDate($model->createdDate),
            '{CONTACT_LINK_ADMIN}' => Yii::$app->urlManagerBackend->createAbsoluteUrl(['contact/manage/view', 'id' => $model->id])
        );

        // Send "Contact Us" to all active admins in DB
        $result = self::sendNotifyToUser($adminModel, $eventID, $tags, $attach);

        // Send copy to user
        $userModel = new User();
        $userModel->id = -2; // Dummy guest
        $userModel->email = $model->email;

        $tags = array(
            '{CIVILITY}' => Html::encode($model->getCivilityCaption()),
            '{USER_NAME}' => Html::encode($model->firstName . ' ' . $model->lastName),
            '{USER_EMAIL}' => Html::encode($model->email),
            '{USER_PHONE}' => Html::encode($model->phone),
            '{USER_COMPANY}' => Html::encode($model->company),
            '{MANDATE}' => Html::encode($model->mandate),
            '{SUBJECT}' => Html::encode($model->subject),
            '{BODY}' => nl2br(Html::encode($model->body)),
            '{DATE}' => DateHelper::getFrenchFormatDbDate($model->createdDate),
        );

        self::sendNotifyToUser($userModel, $userCopyEventID, $tags, $attach);

        return $result;

        /*if ($result = self::sendNotifyToUser($adminModel, $eventID, $tags, $attach)) {
            $userModel = new User();
            $userModel->id = -2; // Dummy guest
            $userModel->email = $model->email;

            $tags = array(
                '{CIVILITY}' => Html::encode($model->getCivilityCaption()),
                '{USER_NAME}' => Html::encode($model->firstName . ' ' . $model->lastName),
                '{USER_EMAIL}' => Html::encode($model->email),
                '{USER_PHONE}' => Html::encode($model->phone),
                '{USER_COMPANY}' => Html::encode($model->company),
                '{MANDATE}' => Html::encode($model->mandate),
                '{SUBJECT}' => Html::encode($model->subject),
                '{BODY}' => nl2br(Html::encode($model->body)),
                '{DATE}' => DateHelper::getFrenchFormatDbDate($model->createdDate),
            );
            
            self::sendNotifyToUser($userModel, $userCopyEventID, $tags, $attach);
        }*/
    }

    /**
     * Sends new "Contact Us" reply from user to admin
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param ContactThread $model
     * @return boolean
     */
    public static function sendContactUsUserReply(ContactThread $model)
    {
        $adminModel = new User();
        $adminModel->id = -1; // Dummy admin
        $adminModel->email = Yii::$app->params['mail']['contactEmail'];

        $tags = array(
            '{CIVILITY}' => Html::encode($model->contact->getCivilityCaption()),
            '{USER_NAME}' => Html::encode($model->contact->firstName . ' ' . $model->contact->lastName),
            '{USER_EMAIL}' => Html::encode($model->contact->email),
            '{USER_PHONE}' => Html::encode($model->contact->phone),
            '{USER_COMPANY}' => Html::encode($model->contact->company),
            '{MANDATE}' => Html::encode($model->contact->mandate),
            '{SUBJECT}' => Html::encode($model->contact->subject),
            '{BODY}' => nl2br(Html::encode($model->body)),
            '{DATE}' => DateHelper::getFrenchFormatDbDate($model->createdDate),
            '{DATE_FIRST}' => DateHelper::getFrenchFormatDbDate($model->contact->createdDate, true),
            '{CONTACT_LINK_ADMIN}' => Yii::$app->urlManagerBackend->createAbsoluteUrl(['contact/manage/view', 'id' => $model->contact->id])
        );

        return self::sendNotifyToUser($adminModel, self::EVENT_CONTACT_US_USER_REPLY, $tags);
    }

    /**
     * Sends new "Contact Us" reply from admin to user
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param ContactThread $model
     * @return boolean
     */
    public static function sendContactUsAdminReply(ContactThread $model)
    {
        if (!$userModel = User::find()->andWhere(['email' => $model->contact->email])->one()) {
            $userModel = new User();
            $userModel->id = -2; // Dummy guest
            $userModel->email = $model->contact->email;
        }

        $tags = array(
            '{CIVILITY}' => Html::encode($model->contact->getCivilityCaption()),
            '{USER_NAME}' => Html::encode($model->contact->firstName . ' ' . $model->contact->lastName),
            '{USER_EMAIL}' => Html::encode($model->contact->email),
            '{USER_PHONE}' => Html::encode($model->contact->phone),
            '{USER_COMPANY}' => Html::encode($model->contact->company),
            '{MANDATE}' => Html::encode($model->contact->mandate),
            '{SUBJECT}' => Html::encode($model->contact->subject),
            '{BODY}' => $model->body,
            '{DATE}' => DateHelper::getFrenchFormatDbDate($model->createdDate),
            '{DATE_FIRST}' => DateHelper::getFrenchFormatDbDate($model->contact->createdDate, true),
            '{CONTACT_LINK_USER}' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['site/contact-reply', 'id' => $model->contact->id, 'code' => $model->contact->code])
        );

        return self::sendNotifyToUser($userModel, self::EVENT_CONTACT_US_ADMIN_REPLY, $tags);
    }

    /**
     * Sends notification to admin about new comment
     *
     * @author Petr Dvukhrechensky <petr.sdkb@gmail.com>
     * @param object $commentBundleModel
     * @param object $commentModel
     * @return boolean
     */
    public static function sendCommentAdminNotify($commentBundleModel, $commentModel)
    {
        $tags = array(
            '{PAGE_TITLE}' => Html::encode($commentBundleModel->nodeTitle),
            '{AUTHOR_NAME}' => Html::encode($commentModel->authorName),
            '{AUTHOR_EMAIL}' => Html::encode($commentModel->authorEmail),
            '{COMMENT}' => Html::encode($commentModel->text),
            '{DATE}' => DateHelper::getFrenchFormatDbDate($commentModel->createdDate, true),
            '{ADMIN_COMMENT_LINK}' => Yii::$app->urlManagerBackend->createAbsoluteUrl(['comments/manage/comments', 'id' => $commentBundleModel->id]),
        );

        $adminModel = new User();
        $adminModel->id = -1; // Dummy admin
        $adminModel->email = Yii::$app->params['mail']['adminEmail'];

        return self::sendNotifyToUser($adminModel, self::EVENT_NEW_COMMENT_ADMIN, $tags);
    }

    /**
     * Sends notification to admin about new form result
     *
     * @param FormPage $formPageModel
     * @param FormPageResult $formResultsModel
     * @return bool
     */
    public static function sendFormPageAdminNotify($formPageModel, $formResultsModel)
    {
        $adminModel = new User();
        $adminModel->id = -1; // Dummy admin
        $adminModel->email = Yii::$app->params['mail']['adminEmail'];

        $subject = Html::encode(Yii::t('notify', 'Results of the form {formName} - {formDate}', [
            'formName' => $formPageModel->title,
            'formDate' => DateHelper::getFrenchFormatDbDate($formResultsModel->createdDate, true)
        ]));

        $formBlocks = $formPageModel->getFormBlocks();

        $body = "<div>";

        foreach ($formResultsModel->items as $item) {
            $formBlockId = FormPage::getFormFieldName($item->questionID);

            if (!isset($formBlocks[$formBlockId]) || !is_object($formBlocks[$formBlockId])) {
                continue;
            }

            $formBlock = $formBlocks[$formBlockId];
            $question = $formBlock->content->label;
            $answer = $item->answer;

            if (FormPage::isListField($formBlock)) {
                $indexedOptions = [];
                foreach ($formBlock->content->options as $option) {
                    $indexedOptions[$option->id] = $option;
                }

                $answer = explode(',', $answer);
                foreach ($answer as $aKey => $aValue) {
                    if (isset($indexedOptions[$aValue])) {
                        $answer[$aKey] = $indexedOptions[$aValue]->text;
                    } else {
                        $answer[$aKey] = '<s>' . Yii::t('notify', 'Option deleted') . '</s>';
                    }
                }
                $answer = join(", ", $answer);
            }

            $body .= "<p><span>{$question}:</span> <b>{$answer}</b></p>";
        }

        $body .= "</div>";

        $footer = Yii::t('notify', 'Find answers to your forms on your back office') . ' '
            . Html::a(
                Yii::$app->urlManagerBackend->createAbsoluteUrl('/'),
                Yii::$app->urlManagerBackend->createAbsoluteUrl(['formpage/manage/results', 'id' => $formPageModel->id]),
                ['target' => '_blank']
            );

        $tags = array(
            '{SUBJECT}' => $subject,
            '{BODY}' => $body,
            '{FOOTER}' => $footer,
        );

        return self::sendNotifyToUser($adminModel, self::EVENT_NEW_FORMPAGE_RESULT_ADMIN, $tags);
    }
}