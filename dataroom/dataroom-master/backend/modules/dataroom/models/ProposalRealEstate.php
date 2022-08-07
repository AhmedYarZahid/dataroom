<?php

namespace backend\modules\dataroom\models;

use backend\modules\document\models\Document;
use common\components\DocumentBehavior;
use Yii;

/**
 * This is the model class for table "ProposalRealEstate".
 *
 * @property integer $proposalID
 * @property integer $documentID
 * @property string $firstName
 * @property string $lastName
 * @property string $address
 * @property string $phone
 * @property integer $kbisID
 * @property integer $cniID
 * @property integer $balanceSheetID
 * @property integer $taxNoticeID
 *
 * @property Proposal $proposal
 * @property Document $document
 * @property Document $kbis
 * @property Document $cni
 * @property Document $balanceSheet
 * @property Document $taxNotice
 */
class ProposalRealEstate extends AbstractProposal
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ProposalRealEstate';
    }

    /**
     * @inheritdoc
     */
    function behaviors()
    {
        return [
            [
                'class' => DocumentBehavior::class,
                'attributes' => [
                    'documentID' => Document::TYPE_PROPOSAL,
                    'kbisID' => Document::TYPE_PROPOSAL,
                    'cniID' => Document::TYPE_PROPOSAL,
                    'balanceSheetID' => Document::TYPE_PROPOSAL,
                    'taxNoticeID' => Document::TYPE_PROPOSAL,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();

        return array_merge($rules, [
            [['documentID', 'firstName', 'lastName', 'address', 'phone'], 'required'],
            [['proposalID'], 'integer'],
            [['firstName'], 'string', 'max' => 50],
            [['lastName'], 'string', 'max' => 70],
            [['address'], 'string', 'max' => 150],
            [['phone'], 'string', 'length' => 10],

            [['documentID', 'kbisID', 'cniID', 'balanceSheetID', 'taxNoticeID'], 'file', 'extensions' => ['pdf','doc','docx','txt','jpg','jpeg','gif','png']],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'proposalID' => Yii::t('app', 'ID'),
            'documentID' => 'Mon offre',
            'firstName' => Yii::t('app', 'First Name'),
            'lastName' => Yii::t('app', 'Last Name / Company name'),
            'address' => Yii::t('app', 'Address'),
            'phone' => Yii::t('app', 'Phone'),
            'kbisID' => Yii::t('app', 'Kbis'),
            'cniID' => Yii::t('app', 'Cni'),
            'balanceSheetID' => Yii::t('app', 'Balance Sheet'),
            'taxNoticeID' => Yii::t('app', 'Tax Notice'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function templatePath()
    {
        return Yii::getAlias('@backend/modules/dataroom/templates/ProposalCompany.doc');
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return Yii::$app->urlManagerBackend->createAbsoluteUrl([
            'dataroom/realestate/proposal/index',
            'ProposalRealEstateSearch[proposalID]' => $this->proposalID
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProposal()
    {
        return $this->hasOne(Proposal::className(), ['id' => 'proposalID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Document::className(), ['id' => 'documentID']);
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
    public function getCni()
    {
        return $this->hasOne(Document::className(), ['id' => 'cniID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBalanceSheet()
    {
        return $this->hasOne(Document::className(), ['id' => 'balanceSheetID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxNotice()
    {
        return $this->hasOne(Document::className(), ['id' => 'taxNoticeID']);
    }
}
