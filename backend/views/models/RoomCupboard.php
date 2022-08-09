<?php

namespace backend\modules\dataroom\models;

use Yii;

/**
 * This is the model class for table "RoomCupboard".
 *
 * @property integer $id
 * @property string $name
 *
 * @property RoomRealEstate2Cupboard[] $roomRealEstate2Cupboards
 * @property RoomRealEstate[] $roomRealEstates
 */
class RoomCupboard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'RoomCupboard';
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
     * Get cupboards list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return RoomCupboard[]
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
    public function getRoomRealEstate2Cupboards()
    {
        return $this->hasMany(RoomRealEstate2Cupboard::className(), ['cupboardID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomRealEstates()
    {
        return $this->hasMany(RoomRealEstate::className(), ['id' => 'roomRealEstateID'])->viaTable('RoomRealEstate2Cupboard', ['cupboardID' => 'id']);
    }
}
