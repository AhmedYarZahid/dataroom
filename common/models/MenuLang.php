<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "MenuLang".
 *
 * @property integer $id
 * @property integer $menuID
 * @property string $languageID
 * @property string $title
 *
 * @property Menu $menu
 * @property Language $language
 */
class MenuLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'MenuLang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menuID', 'languageID', 'title'], 'required'],
            [['menuID'], 'integer'],
            [['languageID'], 'string', 'max' => 2],
            [['title'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'menuID' => Yii::t('app', 'Menu ID'),
            'languageID' => Yii::t('app', 'Language ID'),
            'title' => Yii::t('app', 'Title'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenu()
    {
        return $this->hasOne(Menu::className(), ['id' => 'menuID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'languageID']);
    }
}
