<?php

namespace backend\modules\faq\models;

use Yii;

/**
 * This is the model class for table "FaqItemLang".
 *
 * @property integer $id
 * @property integer $faqItemID
 * @property string $languageID
 * @property string $question
 * @property string $answer
 *
 * @property FaqItem $faqItem
 * @property Language $language
 */
class FaqItemLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'FaqItemLang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['faqItemID', 'languageID'], 'required'],
            [['faqItemID'], 'integer'],
            [['question', 'answer'], 'string'],
            [['languageID'], 'string', 'max' => 2]
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
            'languageID' => Yii::t('app', 'Language ID'),
            'question' => Yii::t('app', 'Question'),
            'answer' => Yii::t('app', 'Answer'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaqItem()
    {
        return $this->hasOne(FaqItem::className(), ['id' => 'faqItemID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'languageID']);
    }
}