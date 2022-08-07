<?php

namespace backend\modules\faq\models;

use common\helpers\ArrayHelper;
use omgdef\multilingual\MultilingualBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "FaqCategory".
 *
 * The followings are the available columns in table 'FaqCategory':
 * @property integer $id
 * @property string $createdDate
 * @property string $updatedDate
 */
class FaqCategory extends ActiveRecord
{
    const HOME_PAGE = 15;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'FaqCategory';
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
                'defaultLanguage' => Yii::$app->params['defaultLanguageID'],
                'requireTranslations' => false,
                'languageField' => 'languageID',
                'langForeignKey' => 'faqCategoryID',
                'tableName' => 'FaqCategoryLang',
                'attributes' => ['title'],

                'dynamicLangClass' => false,
                'langClassName' => FaqCategoryLang::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['create'] = array_merge($this->attributes(), ['title']);
        $scenarios['update'] = $scenarios['create'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['id'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array customized attribute labels (name => label)
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'createdDate' => Yii::t('app', 'Created Date'),
            'updatedDate' => Yii::t('app', 'Updated Date'),
        ];
    }

    /**
     * Get FAQ categories list
     *
     * @return FaqCategory[]
     */
    public static function getList()
    {
        $query = self::find();

        $query->joinWith('translation');

        return $query->all();
    }

    /**
     * Get attribute name based on language (multilingual model) that should be used in a form
     *
     * @param string $attribute
     * @param string $language
     * @return string
     */
    public function getFormAttributeName($attribute, $language)
    {
        return $language == Yii::$app->params['defaultLanguageID'] ? $attribute : $attribute . "_" . $language;
    }

    /**
     * @inheritdoc
     * @return FaqCategoryQuery
     */
    public static function find()
    {
        return new FaqCategoryQuery(get_called_class());
    }
}