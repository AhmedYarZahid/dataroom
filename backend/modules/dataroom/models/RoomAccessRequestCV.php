<?php

namespace backend\modules\dataroom\models;

use backend\modules\document\models\Document;
use common\components\DocumentBehavior;
use Yii;

/**
 * This is the model class for table "RoomAccessRequestCV".
 *
 * @property integer $accessRequestID
 * @property integer $agreementID
 *
 * @property RoomAccessRequest $accessRequest
 * @property Document $agreement
 */
class RoomAccessRequestCV extends AbstractAccessRequest
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'RoomAccessRequestCV';
    }

    function behaviors()
    {
        return [
            [
                'class' => DocumentBehavior::class,
                'attributes' => [
                    'agreementID' => Document::TYPE_ACCESS_REQUEST,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agreementID'], 'required'],
            [['accessRequestID'], 'integer'],
            [['accessRequestID'], 'exist', 'skipOnError' => true, 'targetClass' => RoomAccessRequest::className(), 'targetAttribute' => ['accessRequestID' => 'id']],

            [['agreementID'], 'file', 'extensions' => ['pdf','doc','docx','txt','jpg','jpeg','gif','png']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'accessRequestID' => Yii::t('app', 'ID'),
            'agreementID' => 'Engagement de confidentialité',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccessRequest()
    {
        return $this->hasOne(RoomAccessRequest::className(), ['id' => 'accessRequestID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgreement()
    {
        return $this->hasOne(Document::className(), ['id' => 'agreementID']);
    }

    public function getUrl()
    {
        return Yii::$app->urlManagerBackend->createAbsoluteUrl([
            'dataroom/cv/access-request/index',
            'RoomAccessRequestCVSearch[accessRequestID]' => $this->accessRequestID
        ]);
    }
}
