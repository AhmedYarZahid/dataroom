<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Region".
 *
 * @property integer $id
 * @property string $name
 * @property integer $code
 *
 * @property Department[] $departments
 * @property ProfileCoownership2Region[] $profileCoownership2Regions
 * @property ProfileCoownership[] $profileCoownerships
 * @property ProfileRealEstate2Region[] $profileRealEstate2Regions
 * @property ProfileRealEstate[] $profileRealEstates
 * @property RoomCV[] $roomCVs
 * @property RoomCoownership[] $roomCoownerships
 * @property RoomRealEstate[] $roomRealEstates
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Region';
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
        ];
    }

    /**
     * Get regions list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return Country[]
     */
    public static function getList()
    {
        $query = self::find();

        $query->orderBy(['name' => SORT_ASC]);

        return $query->all();
    }

    /**
     * Get region name with code (magic method)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getNameWithCode()
    {
        return $this->code . ' - ' . $this->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartments()
    {
        return $this->hasMany(Department::className(), ['regionID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfileCoownership2Regions()
    {
        return $this->hasMany(ProfileCoownership2Region::className(), ['regionID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfileCoownerships()
    {
        return $this->hasMany(ProfileCoownership::className(), ['userID' => 'profileCoownershipID'])->viaTable('ProfileCoownership2Region', ['regionID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfileRealEstate2Regions()
    {
        return $this->hasMany(ProfileRealEstate2Region::className(), ['regionID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfileRealEstates()
    {
        return $this->hasMany(ProfileRealEstate::className(), ['userID' => 'profileRealEstateID'])->viaTable('ProfileRealEstate2Region', ['regionID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomCVs()
    {
        return $this->hasMany(RoomCV::className(), ['regionID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomCoownerships()
    {
        return $this->hasMany(RoomCoownership::className(), ['regionID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomRealEstates()
    {
        return $this->hasMany(RoomRealEstate::className(), ['regionID' => 'id']);
    }
}
