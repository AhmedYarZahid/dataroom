<?php

namespace backend\modules\dataroom\models;

use backend\modules\document\models\Document;
use common\components\DocumentBehavior;
use Yii;

/**
 * This is the model class for table "RoomAccessRequestCoownership".
 *
 * @property integer $accessRequestID
 * @property integer $agreementID
 * @property string $personType
 * @property string $candidatePresentation
 * @property integer $identityCardID
 * @property integer $cvID
 * @property integer $lastTaxDeclarationID
 * @property integer $coownershipManagementReferenceID
 * @property string $groupPresentation
 * @property integer $kbisID
 * @property integer $latestCertifiedAccountsID
 * @property integer $capitalAllocationID
 *
 * @property RoomAccessRequest $accessRequest
 * @property Document $identityCard
 * @property Document $cv
 * @property Document $lastTaxDeclaration
 * @property Document $kbis
 * @property Document $coownershipManagementReference
 * @property Document $latestCertifiedAccounts
 * @property Document $capitalAllocation
 * @property Document $agreement
 */
class RoomAccessRequestCoownership extends AbstractAccessRequest
{
    const PERSON_TYPE_PHYSICAL = 'physical';
    const PERSON_TYPE_LEGAL = 'legal';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'RoomAccessRequestCoownership';
    }

    function behaviors()
    {
        return [
            [
                'class' => DocumentBehavior::class,
                'attributes' => [
                    'kbisID' => Document::TYPE_ACCESS_REQUEST,
                    'coownershipManagementReferenceID' => Document::TYPE_ACCESS_REQUEST,
                    'latestCertifiedAccountsID' => Document::TYPE_ACCESS_REQUEST,
                    'capitalAllocationID' => Document::TYPE_ACCESS_REQUEST,
                    'identityCardID' => Document::TYPE_ACCESS_REQUEST,
                    'cvID' => Document::TYPE_ACCESS_REQUEST,
                    'lastTaxDeclarationID' => Document::TYPE_ACCESS_REQUEST,
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
            [['agreementID', 'personType'], 'required'],

            [['candidatePresentation', 'groupPresentation', 'kbisID', 'latestCertifiedAccountsID', 'capitalAllocationID'], 'required', 'when' => function ($model) {
                return $model->personType == self::PERSON_TYPE_LEGAL;
            }, 'whenClient' => "function (attribute, value) {
                return $('#roomaccessrequestcoownership-persontype').val() == '" . self::PERSON_TYPE_LEGAL . "';
            }"],

            [['identityCardID', 'cvID', 'lastTaxDeclarationID', 'coownershipManagementReferenceID'], 'required', 'when' => function ($model) {
                return $model->personType == self::PERSON_TYPE_PHYSICAL;
            }, 'whenClient' => "function (attribute, value) {
                return $('#roomaccessrequestcoownership-persontype').val() == '" . self::PERSON_TYPE_PHYSICAL . "';
            }"],

            [['accessRequestID'], 'integer'],
            [['personType', 'candidatePresentation', 'groupPresentation'], 'string'],

            [['accessRequestID'], 'exist', 'skipOnError' => true, 'targetClass' => RoomAccessRequest::className(), 'targetAttribute' => ['accessRequestID' => 'id']],

            [['kbisID', 'coownershipManagementReferenceID', 'latestCertifiedAccountsID', 'capitalAllocationID', 'identityCardID', 'cvID', 'lastTaxDeclarationID'], 'file', 'extensions' => ['pdf','doc','docx','txt','jpg','jpeg','gif','png']],

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
            'agreementID' => 'Engagement de confidentialit??',
            'personType' => Yii::t('app', 'Person Type'),
            'candidatePresentation' => Yii::t('app', 'Candidate Presentation'),
            'identityCardID' => Yii::t('app', 'Identity Card ID'),
            'cvID' => Yii::t('app', 'Cv ID'),
            'lastTaxDeclarationID' => Yii::t('app', 'Last Tax Declaration ID'),
            'coownershipManagementReferenceID' => Yii::t('app', 'Coownership Management Reference ID'),
            'groupPresentation' => Yii::t('app', 'Group Presentation'),
            'kbisID' => Yii::t('app', 'Kbis ID'),
            'latestCertifiedAccountsID' => Yii::t('app', 'Latest Certified Accounts ID'),
            'capitalAllocationID' => Yii::t('app', 'Capital Allocation ID'),
        ];
    }

    /**
     * Get possible person types
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getPersonTypes($exclude = [])
    {
        $list = [
            self::PERSON_TYPE_PHYSICAL => Yii::t('app', 'Physical'),
            self::PERSON_TYPE_LEGAL => Yii::t('app', 'Legal'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return person type caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getPersonTypeCaption($value)
    {
        $list = self::getPersonTypes();

        return isset($list[$value]) ? $list[$value] : null;
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
    public function getIdentityCard()
    {
        return $this->hasOne(Document::className(), ['id' => 'identityCardID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCv()
    {
        return $this->hasOne(Document::className(), ['id' => 'cvID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastTaxDeclaration()
    {
        return $this->hasOne(Document::className(), ['id' => 'lastTaxDeclarationID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKbis()
    {
        return $this->hasOne(Document::className(), ['id' => 'kbisID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoownershipManagementReference()
    {
        return $this->hasOne(Document::className(), ['id' => 'coownershipManagementReferenceID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLatestCertifiedAccounts()
    {
        return $this->hasOne(Document::className(), ['id' => 'latestCertifiedAccountsID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCapitalAllocation()
    {
        return $this->hasOne(Document::className(), ['id' => 'capitalAllocationID']);
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
            'dataroom/coownership/access-request/index',
            'RoomAccessRequestCoownershipSearch[accessRequestID]' => $this->accessRequestID
        ]);
    }
}
