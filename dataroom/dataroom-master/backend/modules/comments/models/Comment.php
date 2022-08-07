<?php

namespace backend\modules\comments\models;

use Yii;
use backend\modules\comments\models\CommentBundle;

/**
 * This is the model class for table "Comment".
 *
 * @property integer $id
 * @property integer $commentBundleID
 * @property string $authorName
 * @property string $authorEmail
 * @property string $text
 * @property integer $isApproved
 * @property integer $approvedDate
 *
 * @property CommentBundle $commentBundle
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @var string Verification code to be entered by guest
     */
    public $verifyCode;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['commentBundleID', 'authorName', 'text'], 'required'],
            [['commentBundleID', 'isApproved'], 'integer'],
            [['authorEmail'], 'email'],
            [['authorName', 'text', 'approvedDate'], 'safe'],
            [['text'], 'string', 'max' => 10000],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha', 'when' => function ($model) { return Yii::$app->user->isGuest; }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'commentBundleID' => Yii::t('app', 'Page ID'),
            'authorName' => Yii::t('app', 'Name'),
            'authorEmail' => Yii::t('app', 'Email'),
            'text' => Yii::t('app', 'Comment'),
            'isApproved' => Yii::t('app', 'Is Approved'),
            'approvedDate' => Yii::t('app', 'Approved Date'),
            'verifyCode' => Yii::t('contact', 'Verify Code'),
        ];
    }

    /**
     * Gets comment by ID
     *
     * @param int $id
     * @return Comment
     */
    public static function getModel($id)
    {
        return self::findOne($id);
    }
}
