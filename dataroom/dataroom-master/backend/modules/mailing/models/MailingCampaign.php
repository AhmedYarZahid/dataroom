<?php

namespace backend\modules\mailing\models;

use backend\modules\dataroom\models\Room;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use common\models\User;

/**
 * This is the model class for table "MailingCampaign".
 *
 * @property integer $id
 * @property integer $listID
 * @property integer $userID
 * @property integer $roomID
 * @property string $sender
 * @property string $subject
 * @property string $body
 * @property string $status
 * @property string $createdDate
 * @property string $updatedDate
 * @property string $sentDate
 *
 * @property MailingList $list
 * @property User $user
 * @property Room $room
 */
class MailingCampaign extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE_OR_UPDATE = 'create_or_update';
    const SCENARIO_TEST_EMAIL = 'test_email';
    const SCENARIO_SEND = 'send';
    const SCENARIO_EMAIL_TO_ROOM_USERS = 'email_to_room_users';

    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';

    public $testTo;
    public $recipientIDs;

    // --- Paths to images in email template (for constructing correct url to images) --- //
    private static $imagesPaths = array('/uploads/editor/');

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'MailingCampaign';
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
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        
        $scenarios[self::SCENARIO_CREATE_OR_UPDATE] = $scenarios['default'];
        $scenarios[self::SCENARIO_TEST_EMAIL] = $scenarios['default'];
        $scenarios[self::SCENARIO_SEND] = $scenarios['default'];
        $scenarios[self::SCENARIO_EMAIL_TO_ROOM_USERS] = $scenarios['default'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sender', 'subject', 'body'], 'required'],

            [['listID'], 'required', 'on' => self::SCENARIO_SEND],

            [['listID', 'userID', 'roomID'], 'integer'],
            [['body', 'status'], 'string'],
            [['createdDate', 'updatedDate', 'sentDate', 'testTo'], 'safe'],
            [['sender', 'subject'], 'string', 'max' => 255],
            [['listID'], 'exist', 'skipOnError' => true, 'targetClass' => MailingList::className(), 'targetAttribute' => ['listID' => 'id']],
            [['userID'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userID' => 'id']],

            ['recipientIDs', 'required', 'on' => self::SCENARIO_EMAIL_TO_ROOM_USERS],
            ['recipientIDs', 'each', 'rule' => ['integer']],

            ['sender', 'email'],

            ['testTo', 'required', 'on' => self::SCENARIO_TEST_EMAIL],
            ['testTo', 'email', 'on' => self::SCENARIO_TEST_EMAIL],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'listID' => Yii::t('admin', 'Mailing list'),
            'userID' => Yii::t('admin', 'Created by'),
            'roomID' => Yii::t('admin', 'Campaign about room'),
            'sender' => Yii::t('admin', 'Sender'),
            'subject' => Yii::t('admin', 'Subject'),
            'body' => Yii::t('admin', 'Body'),
            'status' => Yii::t('admin', 'Status'),
            'createdDate' => Yii::t('admin', 'Created Date'),
            'updatedDate' => Yii::t('admin', 'Updated Date'),
            'sentDate' => Yii::t('admin', 'Sent date'),
            'uniqueName' => Yii::t('admin', 'Code'),
            'testTo' => Yii::t('admin', 'Recipient of test email'),
            'recipientIDs' => Yii::t('admin', 'Recipients'),
        ];
    }

    /**
     * Get room name
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return null|string
     */
    public function getRoomName()
    {
        return $this->room ? $this->room->title : null;
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
    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'roomID']);
    }

    public function listOptions()
    {
        $lists = MailingList::find()->all();

        return ArrayHelper::map($lists, 'id', 'name');
    }

    public function statusOptions()
    {
        return [
            self::STATUS_DRAFT => Yii::t('admin', 'Draft'),
            self::STATUS_SENT => Yii::t('admin', 'Sent'),
        ];
    }

    public function getStatusCaption()
    {
        $list = $this->statusOptions();

        return isset($list[$this->status]) ? $list[$this->status] : null;
    }

    public function getUniqueName()
    {
        $name = 'LIST_' . $this->listID . '_' . $this->id;

        if (YII_ENV == 'local' || YII_ENV == 'dev') {
            $name .= '_' . strtoupper(YII_ENV);
        }

        return $name;
    }

    /**
     * Get correctly formatted content for email
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getFullBody()
    {
        // Construct full paths to images
        //$siteUrl = Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/'], 'https');
        $siteUrl = Yii::$app->urlManagerFrontend->hostInfo;

        if ($siteUrl[strlen($siteUrl) - 1] == '/') {
            $siteUrl = substr($siteUrl, 0, -1);
        }

        foreach (self::$imagesPaths as $path) {
            $imagesPath = preg_quote($path, '#');
            $this->body = preg_replace('#(src=")(' . $imagesPath . '.+?")#', '$1' . $siteUrl . '$2', $this->body);
        }

        // Add header & footer
        return self::getHtmlHeader() . $this->body . self::getHtmlFooter();
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
        return Yii::$app->controller->renderPartial('@backend/modules/mailing/views/campaign/_mail-header');
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
        return Yii::$app->controller->renderPartial('@backend/modules/mailing/views/campaign/_mail-footer');
    }
}