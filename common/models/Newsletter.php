<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "Newsletter".
 *
 * @property integer $id
 * @property string $email
 * @property string $languageID
 * @property integer $isActive
 * @property string $createdDate
 *
 * @property Language $language
 */
class Newsletter extends \yii\db\ActiveRecord
{
    const SCENARIO_NEWSLETTER_FORM = 'newsletter_form';

    const PROFESSION_MEMBER_AJA = 16;

    public $verifyCode;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Newsletter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['createdDate'], 'safe'],
            [['email'], 'string', 'max' => 150],
            [['languageID'], 'string', 'max' => 2],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['firstName', 'lastName'], 'string', 'max' => 50],
            [['profession', 'userID', 'isActive'], 'integer'],

            [['firstName', 'lastName'], 'required', 'on' => self::SCENARIO_NEWSLETTER_FORM],
            ['verifyCode', 'captcha', 'when' => function ($model) {
                return Yii::$app->user->isGuest;
            }, 'on' => self::SCENARIO_NEWSLETTER_FORM],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Your email'),
            'firstName' => Yii::t('app', 'First Name'),
            'lastName' => Yii::t('app', 'Name'),
            'profession' => Yii::t('app', 'Profession'),
            'languageID' => Yii::t('app', 'Language'),
            'createdDate' => Yii::t('app', 'Created Date'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'languageID']);
    }

    public function professionList($addAdminProfessions = false)
    {
//        $list = [
//            0 => 'Agences immobilières et conseils immobiliers',
//            1 => 'Managers de crise et de transition',
//            2 => "Fond d’investissement et de retournement",
//            3 => 'Investisseurs privés',
//            4 => 'Auditeurs',
//            5 => 'Experts comptables',
//            6 => 'Family offices',
//            7 => 'Magistrats',
//            8 => 'Avocats',
//            9 => 'Mandataires de justice',
//            10 => 'Particuliers',
//            11 => 'Banques et établissements de crédits',
//            12 => 'Journalistes',
//            13 => 'Autres professions juridiques',
//            14 => "Chefs d’entreprises",
//            15 => "Conseils en stratégie/management/organisation/financiers/RH",
//            999 => "Non défini",
//        ];
        if ($addAdminProfessions){
            $list = ArrayHelper::map(\common\models\Profession::find()->all(),"id","name");
        }else{
            $list = ArrayHelper::map(\common\models\Profession::find()->andWhere(['!=','type','admin'])->all(),"id","name");
        }
//
//        if ($addAdminProfessions) {
//            $list[self::PROFESSION_MEMBER_AJA] = 'Membre d’AJAssociés';
//        }

        return $list;
    }

    public function professionCaption()
    {
        $list = $this->professionList();
        return isset($list[$this->profession]) ? $list[$this->profession] : null;
    }

    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * Checks whether a user can receive emailing (is visible in AJA list module).
     *
     * @return bool
     */
    public function canReceiveMailing()
    {
        return $this->isActive;
    }
}
