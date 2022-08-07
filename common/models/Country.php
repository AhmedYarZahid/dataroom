<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Country".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property integer $isDefault
 *
 * @property RoomRealEstate[] $roomRealEstates
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['code'], 'string', 'max' => 2],
            [['isDefault'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
            'isDefault' => Yii::t('app', 'Is Default'),
        ];
    }

    /**
     * Get contries list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return Country[]
     */
    public static function getList()
    {
        $query = self::find();

        $query->orderBy(['isDefault' => SORT_DESC, 'name' => SORT_ASC]);

        return $query->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomRealEstates()
    {
        return $this->hasMany(RoomRealEstate::className(), ['countryID' => 'id']);
    }
}
