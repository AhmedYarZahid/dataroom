<?php

namespace common\models;

use kartik\helpers\Html;
use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "Language".
 *
 * @property string $id
 * @property string $locale
 * @property string $name
 * @property integer $isDefault
 * @property string $createdDate
 * @property string $updatedDate
 *
 * @property MenuLang[] $menuLangs
 * @property Newsletter[] $newsletters
 * @property User[] $users
 */
class Language extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Language';
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'unique'],
            [['isDefault'], 'integer'],
            [['createdDate', 'updatedDate'], 'safe'],
            [['id'], 'string', 'max' => 2],
            [['locale'], 'string', 'max' => 5],
            [['name'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'locale' => Yii::t('app', 'Locale'),
            'name' => Yii::t('app', 'Name'),
            'isDefault' => Yii::t('app', 'Is Default'),
            'createdDate' => Yii::t('app', 'Created Date'),
            'updatedDate' => Yii::t('app', 'Updated Date'),
        ];
    }

    /**
     * Get translated language name by ID
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $id
     * @return string
     * @throws Exception
     */
    public static function getTranslatedNameByID($id)
    {
        if (!$id) {
            return '';
        }

        switch ($id) {
            case 'en':
                $langName = Yii::t('app', 'English');

                break;

            case 'fr':
                $langName = Yii::t('app', 'French');

                break;

            default:
                //throw new Exception('Not supported language id: ' . $id);
                return null;
        }

        return $langName;
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if ($name == 'name') {
            $result = self::getTranslatedNameByID($this->id);

            if ($result === null) {
                $result = $this->getAttribute('name');
            }

            return $result;
        }

        return parent::__get($name);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->isDefault) {
            self::updateAll(['isDefault' => 0], 'id != :id', [':id' => $this->id]);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Get languages list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return Language[]
     */
    public static function getList()
    {
        $query = self::find();

        $query->orderBy(['isDefault' => SORT_DESC, 'name' => SORT_ASC]);

        return $query->all();
    }

    /**
     * Get default language
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param bool $returnOnlyID
     *
     * @return string|Language
     */
    public static function getDefaultLanguage($returnOnlyID = false)
    {
        $query = self::find();

        $query->where(['isDefault' => 1]);

        if ($returnOnlyID) {
            $query->select(['id']);

            return $query->scalar();
        } else {
            return $query->one();
        }
    }

    /**
     * Get icon image in html
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getIconHtml()
    {
        return Html::img('/images/lang-icons/' . $this->id . '.png', ['title' => $this->name, 'alt' => $this->name]);
    }

    /**
     * Check if possible to remove language
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return bool
     */
    public function isAllowDelete()
    {
        return !$this->isDefault;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuLangs()
    {
        return $this->hasMany(MenuLang::className(), ['languageID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNewsletters()
    {
        return $this->hasMany(Newsletter::className(), ['languageID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['languageID' => 'id']);
    }
}
