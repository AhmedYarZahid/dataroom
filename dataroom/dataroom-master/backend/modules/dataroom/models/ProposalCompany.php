<?php

namespace backend\modules\dataroom\models;

use Yii;
use common\components\DocumentBehavior;
use backend\modules\document\models\Document;


/**
 * This is the model class for table "ProposalCompany".
 *
 * @property integer $proposalID
 * @property integer $documentID
 * @property string $tangibleAmount
 * @property string $intangibleAmount
 * @property string $stock
 * @property string $workInProgress
 * @property string $loansRecovery
 * @property integer $paidLeave
 * @property string $other
 * @property string $employersNumber
 *
 * @property Proposal $proposal
 */
class ProposalCompany extends AbstractProposal
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ProposalCompany';
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
            [['documentID', 'tangibleAmount', 'intangibleAmount', 'stock', 'workInProgress', 'loansRecovery', 'paidLeave', 'other', 'employersNumber'], 'required'],
            [['proposalID'], 'integer'],
            ['paidLeave', 'boolean'],
            [['other'], 'string'],
            [['tangibleAmount', 'intangibleAmount', 'employersNumber'], 'string', 'max' => 50],
            [['stock', 'workInProgress', 'loansRecovery'], 'string', 'max' => 255],
            [['proposalID'], 'exist', 'skipOnError' => true, 'targetClass' => Proposal::className(), 'targetAttribute' => ['proposalID' => 'id']],

            ['documentID', 'file', 'extensions' => ['pdf','doc','docx','txt','jpg','jpeg','gif','png']],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'proposalID' => 'ID',
            'documentID' => 'Mon offre',
            'tangibleAmount' => 'Montant corporels',
            'intangibleAmount' => 'Montant incorporels',
            'stock' => 'Stock',
            'workInProgress' => 'Travaux en cours',
            'loansRecovery' => 'L642-12 CC (reprise d’emprunt)',
            'paidLeave' => 'Congé payé repris',
            'other' => 'Autres',
            'employersNumber' => 'Nombre de salariés repris',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoc()
    {
        return $this->hasOne(Document::className(), ['id' => 'documentID']);
    }

    public function templatePath()
    {
        return Yii::getAlias('@backend/modules/dataroom/templates/ProposalCompany.doc');
    }

    public function getUrl()
    {
        return Yii::$app->urlManagerBackend->createAbsoluteUrl([
            'dataroom/companies/proposal/index', 
            'ProposalCompanySearch[proposalID]' => $this->proposalID
        ]);
    }
}