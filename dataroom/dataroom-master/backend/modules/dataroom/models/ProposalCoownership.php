<?php

namespace backend\modules\dataroom\models;

use backend\modules\document\models\Document;
use common\components\DocumentBehavior;
use Yii;

/**
 * This is the model class for table "ProposalCoownership".
 *
 * @property integer $proposalID
 * @property integer $documentID
 * @property string $companyName
 * @property string $fullName
 * @property string $address
 * @property string $phone
 * @property integer $kbisID
 * @property integer $cniID
 * @property integer $businessCardID
 *
 * @property Proposal $proposal
 * @property Document $document
 * @property Document $kbis
 * @property Document $cni
 * @property Document $businessCard
 */
class ProposalCoownership extends AbstractProposal
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ProposalCoownership';
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
                    'businessCardID' => Document::TYPE_PROPOSAL,
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
            [['documentID', 'companyName', 'fullName', 'address', 'phone', 'kbisID', 'cniID', 'businessCardID'], 'required'],
            [['proposalID'], 'integer'],
            [['companyName'], 'string', 'max' => 50],
            [['fullName'], 'string', 'max' => 100],
            [['address'], 'string', 'max' => 150],
            [['phone'], 'string', 'length' => 10],

            [['documentID', 'kbisID', 'cniID', 'businessCardID'], 'file', 'extensions' => ['pdf','doc','docx','txt','jpg','jpeg','gif','png']],
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
            'companyName' => Yii::t('app', 'Company Name'),
            'fullName' => Yii::t('app', 'Full Name'),
            'address' => Yii::t('app', 'Address'),
            'phone' => Yii::t('app', 'Phone'),
            'kbisID' => Yii::t('app', 'Kbis'),
            'cniID' => Yii::t('app', 'Cni'),
            'businessCardID' => Yii::t('app', 'Business Card'),
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
            'dataroom/coownership/proposal/index',
            'ProposalCoownershipSearch[proposalID]' => $this->proposalID
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
    public function getBusinessCard()
    {
        return $this->hasOne(Document::className(), ['id' => 'businessCardID']);
    }
}
