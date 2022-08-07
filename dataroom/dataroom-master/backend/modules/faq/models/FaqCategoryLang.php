<?php

namespace backend\modules\faq\models;

use Yii;

/**
 * This is the model class for table "FaqCategoryLang".
 *
 * @property integer $id
 * @property integer $faqCategoryID
 * @property string $languageID
 * @property string $title
 *
 * @property FaqCategory $faqCategory
 * @property Language $language
 */
class FaqCategoryLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'FaqCategoryLang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['faqCategoryID', 'languageID'], 'required'],
            [['faqCategoryID'], 'integer'],
            [['languageID'], 'string', 'max' => 2],
            [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'faqCategoryID' => Yii::t('app', 'Faq Category ID'),
            'languageID' => Yii::t('app', 'Language ID'),
            'title' => Yii::t('app', 'Title'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaqCategory()
    {
        return $this->hasOne(FaqCategory::className(), ['id' => 'faqCategoryID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'languageID']);
    }
}