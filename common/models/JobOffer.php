<?php

namespace common\models;

use common\helpers\ArrayHelper;
use omgdef\multilingual\MultilingualBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "JobOffer".
 *
 * @property integer $id
 * @property string $contactEmail
 * @property string $salary
 * @property string $currency
 * @property string $contractType
 * @property string $startDate
 * @property string $expiryDate
 * @property string $isRemoved
 * @property string $createdDate
 * @property string $updatedDate
 *
 * @property JobOfferLang[] $jobOfferLangs
 */
class JobOffer extends \yii\db\ActiveRecord
{
    const CURRENCY_EUR = 'eur';
    const CURRENCY_USD = 'usd';

    const CONTRACT_TYPE_CDI = 'cdi';
    const CONTRACT_TYPE_CDD = 'cdd';
    const CONTRACT_TYPE_STAGE = 'stage';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'JobOffer';
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
            [
                'class' => MultilingualBehavior::className(),
                'languages' => ArrayHelper::map(Yii::$app->params['languagesList'], 'id', 'name'),
                'languageField' => 'languageID',
                'requireTranslations' => true,
                'defaultLanguage' => Yii::$app->params['defaultLanguageID'],
                'langForeignKey' => 'jobOfferID',
                'tableName' => 'JobOfferLang',
                'attributes' => ['title', 'location', 'description', 'skills'],

                //'localizedPrefix' => '',
                //'dynamicLangClass' => true,
                //'langClassName' => PostLang::className(), // or namespace/for/a/class/PostLang
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'location', 'description', 'skills', 'contactEmail', 'contractType', 'salary', 'startDate', 'publicationDate', 'expiryDate'], 'required'],
            [['salary', 'location'], 'string', 'max' => 255],
            [['currency', 'contractType'], 'string'],
            [['startDate', 'expiryDate', 'createdDate', 'updatedDate', 'publicationDate'], 'safe'],
            [['contactEmail'], 'string', 'max' => 150],
            [['contactEmail'], 'email'],
            ['startDate','validateDates'],
        ];
    }

    /**
     * Validate start/expiry dates
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $attr
     */
    public function validateDates($attr)
    {
        if (strtotime($this->expiryDate) < strtotime($this->startDate)) {
            $this->addError('startDate', Yii::t('admin', 'Please give correct Start and Expiry dates'));
            $this->addError('expiryDate', Yii::t('admin', 'Please give correct Start and Expiry dates'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'contactEmail' => Yii::t('app', 'Contact Email'),
            'salary' => Yii::t('app', 'Salary'),
            'currency' => Yii::t('app', 'Currency'),
            'contractType' => Yii::t('app', 'Contract Type'),
            'startDate' => Yii::t('app', 'Start Date'),
            'publicationDate' => Yii::t('app', 'Publication date'),
            'expiryDate' => Yii::t('app', 'Expiry Date'),
            'createdDate' => Yii::t('app', 'Created Date'),
            'updatedDate' => Yii::t('app', 'Updated Date'),

            'title' => Yii::t('app', 'Title'),
            'location' => Yii::t('app', 'Location'),
            'description' => Yii::t('app', 'Description'),
            'skills' => Yii::t('app', 'Skills'),

            'title_fr' => Yii::t('app', 'Title'),
            'location_fr' => Yii::t('app', 'Location'),
            'description_fr' => Yii::t('app', 'Description'),
            'skills_fr' => Yii::t('app', 'Skills'),

            'title_en' => Yii::t('app', 'Title'),
            'location_en' => Yii::t('app', 'Location'),
            'description_en' => Yii::t('app', 'Description'),
            'skills_en' => Yii::t('app', 'Skills'),
        ];
    }

    /**
     * Get currencies list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return array
     */
    public static function getCurrencyList()
    {
        $result = [
            self::CURRENCY_EUR => '€',
            self::CURRENCY_USD => '$',
        ];

        return $result;
    }

    /**
     * Get currency sign
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $value
     * @return string
     */
    public static function getCurrencySign($value)
    {
        $list = self::getCurrencyList();

        return $list[$value];
    }

    /**
     * Get possible contract types
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     * @return array
     */
    public static function getContractTypes($exclude = [])
    {
        $result = [
            self::CONTRACT_TYPE_CDI => Yii::t('app', 'CDI'),
            self::CONTRACT_TYPE_CDD => Yii::t('app', 'CDD'),
            self::CONTRACT_TYPE_STAGE => Yii::t('app', 'Stage'),
        ];

        return array_diff_key($result, array_flip($exclude));
    }

    /**
     * Return contract type caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getContractTypeCaption($value)
    {
        $list = self::getContractTypes();

        return isset($list[$value]) ? $list[$value] : null;
    }

    /**
     * Get job offers list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $limit
     * @return JobOffer[]
     */
    public static function getList($limit = null)
    {
        $query = self::find();

        $query->removed(false);

        if ($limit) {
            $query->limit($limit);
        }

        $query->orderBy(['startDate' => SORT_DESC]);

        return $query->all();
    }

    /**
     * Get salary with currency sign
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getSalaryWithCurrency()
    {
        return $this->salary != null ? Yii::$app->formatter->asDecimal($this->salary) . ' ' . $this->getCurrencySign($this->currency) : null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobOfferLangs()
    {
        return $this->hasMany(JobOfferLang::className(), ['jobOfferID' => 'id']);
    }

    /**
     * @inheritdoc
     * @return JobOfferQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new JobOfferQuery(get_called_class());
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
}
