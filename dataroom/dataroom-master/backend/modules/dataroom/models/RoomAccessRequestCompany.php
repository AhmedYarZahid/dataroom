<?php

namespace backend\modules\dataroom\models;

use Yii;
use common\components\DocumentBehavior;
use backend\modules\document\models\Document;

/**
 * This is the model class for table "RoomAccessRequestCompany".
 *
 * @property integer $accessRequestID
 * @property string $presentation
 * @property integer $kbis
 * @property integer $balanceSheet
 * @property integer $cni
 * @property integer $commitment
 *
 * @property RoomAccessRequest $accessRequest
 */
class RoomAccessRequestCompany extends AbstractAccessRequest
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'RoomAccessRequestCompany';
    }

    function behaviors()
    {
        return [
            [
                'class' => DocumentBehavior::class,
                'attributes' => [
                    'kbis' => Document::TYPE_ACCESS_REQUEST,
                    'balanceSheet' => Document::TYPE_ACCESS_REQUEST,
                    'cni' => Document::TYPE_ACCESS_REQUEST,
                    'commitment' => Document::TYPE_ACCESS_REQUEST,
                ],
                'scenarios' => ['default', 'insert', 'update', 'delete'],
            ],
       ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['presentation', 'kbis', 'balanceSheet', 'cni', 'commitment'], 'required'],
            [['accessRequestID'], 'integer'],
            [['presentation'], 'string', 'max' => 255],
            [['accessRequestID'], 'exist', 'skipOnError' => true, 'targetClass' => RoomAccessRequest::className(), 'targetAttribute' => ['accessRequestID' => 'id']],

            [['kbis', 'balanceSheet', 'cni', 'commitment'], 'file', 'extensions' => ['pdf','doc','docx','txt','jpg','jpeg','gif','png']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'accessRequestID' => 'ID',
            'presentation' => 'Présentation du candidat',
            'kbis' => 'Kbis',
            'balanceSheet' => 'Bilan',
            'cni' => 'CNI',
            'commitment' => 'Engagement de confidentialité',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKbisDoc()
    {
        return $this->hasOne(Document::className(), ['id' => 'kbis']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBalanceSheetDoc()
    {
        return $this->hasOne(Document::className(), ['id' => 'balanceSheet']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCniDoc()
    {
        return $this->hasOne(Document::className(), ['id' => 'cni']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommitmentDoc()
    {
        return $this->hasOne(Document::className(), ['id' => 'commitment']);
    }

    public function getUrl()
    {
        return Yii::$app->urlManagerBackend->createAbsoluteUrl([
            'dataroom/companies/access-request/index', 
            'RoomAccessRequestCompanySearch[accessRequestID]' => $this->accessRequestID
        ]);
    }
}