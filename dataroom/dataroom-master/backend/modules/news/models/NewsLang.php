<?php

namespace backend\modules\news\models;

use Yii;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "NewsLang".
 *
 * @property integer $id
 * @property integer $newsID
 * @property string $languageID
 * @property string $title
 * @property string $body
 * @property string $slug
 *
 * @property News $news
 * @property Language $language
 */
class NewsLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'NewsLang';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title',
                'slugAttribute' => 'slug',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'body'], 'required'],
            [['newsID'], 'integer'],
            [['body'], 'string'],
            [['languageID'], 'string', 'max' => 2],
            [['title', 'slug'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'newsID' => Yii::t('app', 'News ID'),
            'languageID' => Yii::t('app', 'Language ID'),
            'title' => Yii::t('app', 'Title'),
            'body' => Yii::t('app', 'Body'),
            'slug' => Yii::t('app', 'Slug'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasOne(News::className(), ['id' => 'newsID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'languageID']);
    }
}
