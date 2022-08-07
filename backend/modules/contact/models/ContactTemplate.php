<?php

namespace backend\modules\contact\models;

use common\helpers\ArrayHelper;
use omgdef\multilingual\MultilingualBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ContactTemplate".
 *
 * @property integer $id
 * @property string $name
 * @property string $body
 */
class ContactTemplate extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ContactTemplate';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => MultilingualBehavior::className(),
                'languages' => ArrayHelper::map(Yii::$app->params['languagesList'], 'id', 'name'),
                'defaultLanguage' => Yii::$app->params['defaultLanguageID'],
                'languageField' => 'languageID',
                'requireTranslations' => true,
                'langForeignKey' => 'contactTemplateID',
                'tableName' => 'ContactTemplateLang',
                'attributes' => ['name', 'body'],

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
            [['name', 'body'], 'required'],
            [['body'], 'string'],
            [['name'], 'string', 'max' => 252]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('contact', 'ID'),
            'name' => Yii::t('contact', 'Name'),
            'body' => Yii::t('contact', 'Template Body'),
            'name_fr' => Yii::t('app', 'Name'),
            'body_fr' => Yii::t('app', 'Template Body'),
            'name_en' => Yii::t('app', 'Name'),
            'body_en' => Yii::t('app', 'Template Body'),
        ];
    }

    /**
     * @inheritdoc
     * @return ContactTemplateQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ContactTemplateQuery(get_called_class());
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
