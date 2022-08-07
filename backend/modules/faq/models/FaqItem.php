<?php

namespace backend\modules\faq\models;

use Yii;
use common\helpers\ArrayHelper;
use omgdef\multilingual\MultilingualBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "FaqItem".
 *
 * @property integer $id
 * @property integer $faqCategoryID
 * @property string $createdDate
 * @property string $updatedDate
 */
class FaqItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'FaqItem';
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
                'langForeignKey' => 'faqItemID',
                'tableName' => 'FaqItemLang',
                'attributes' => ['question', 'answer'],

                'dynamicLangClass' => false,
                'langClassName' => FaqItemLang::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['create'] = array_merge($this->attributes(), ['question', 'answer']);
        $scenarios['update'] = $scenarios['create'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['faqCategoryID', 'question', 'answer'], 'required'],
            [['faqCategoryID'], 'integer'],
            [['question', 'answer', 'createdDate', 'updatedDate'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'faqItemID' => Yii::t('app', 'Faq Item ID'),
            'createdDate' => Yii::t('app', 'Created Date'),
            'updatedDate' => Yii::t('app', 'Updated Date'),
            'question' => Yii::t('faq', 'Question'),
            'answer' => Yii::t('faq', 'Answer'),
        ];
    }

    /**
     * @inheritdoc
     * @return FaqCategoryQuery
     */
    public static function find()
    {
        return new FaqCategoryQuery(get_called_class());
    }

    /**
     * Get FAQ items by category
     *
     * @param string $categoryID
     * @return FaqItem[]
     */
    public static function getList($categoryID)
    {
        $query = self::find();

        $query->joinWith('translation');
        $query->andWhere(['faqCategoryID' => $categoryID]);

        return $query->all();
    }
}