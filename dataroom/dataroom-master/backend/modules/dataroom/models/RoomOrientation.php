<?php

namespace backend\modules\dataroom\models;

use Yii;

/**
 * This is the model class for table "RoomOrientation".
 *
 * @property integer $id
 * @property string $name
 *
 * @property RoomRealEstate2Orientation[] $roomRealEstate2Orientations
 * @property RoomRealEstate[] $roomRealEstates
 */
class RoomOrientation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'RoomOrientation';
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
     * Get room orientations list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return RoomOrientation[]
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
    public function getRoomRealEstate2Orientations()
    {
        return $this->hasMany(RoomRealEstate2Orientation::className(), ['orientationID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomRealEstates()
    {
        return $this->hasMany(RoomRealEstate::className(), ['id' => 'roomRealEstateID'])->viaTable('RoomRealEstate2Orientation', ['orientationID' => 'id']);
    }
}
