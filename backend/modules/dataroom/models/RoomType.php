<?php

namespace backend\modules\dataroom\models;

use Yii;

/**
 * This is the model class for table "RoomType".
 *
 * @property integer $id
 * @property string $name
 *
 * @property RoomRealEstate2RoomType[] $roomRealEstate2RoomTypes
 * @property RoomRealEstate[] $roomRealEstates
 */
class RoomType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'RoomType';
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
     * Get room types list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return RoomType[]
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
    public function getRoomRealEstate2RoomTypes()
    {
        return $this->hasMany(RoomRealEstate2RoomType::className(), ['roomTypeID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomRealEstates()
    {
        return $this->hasMany(RoomRealEstate::className(), ['id' => 'roomRealEstateID'])->viaTable('RoomRealEstate2RoomType', ['roomTypeID' => 'id']);
    }
}
