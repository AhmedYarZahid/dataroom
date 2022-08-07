<?php

namespace backend\modules\dataroom\models;

use Yii;

/**
 * This is the model class for table "RoomFacility".
 *
 * @property integer $id
 * @property string $name
 *
 * @property RoomRealEstate2Facility[] $roomRealEstate2Facilities
 * @property RoomRealEstate[] $roomRealEstates
 */
class RoomFacility extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'RoomFacility';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 70],
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
        ];
    }

    /**
     * Get facilities list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return RoomFacility[]
     */
    public static function getList()
    {
        $query = self::find();

        $query->orderBy(['name' => SORT_ASC]);

        return $query->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomRealEstate2Facilities()
    {
        return $this->hasMany(RoomRealEstate2Facility::className(), ['facilityID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomRealEstates()
    {
        return $this->hasMany(RoomRealEstate::className(), ['id' => 'roomRealEstateID'])->viaTable('RoomRealEstate2Facility', ['facilityID' => 'id']);
    }
}
