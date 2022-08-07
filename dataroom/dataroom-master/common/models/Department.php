<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Department".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property integer $regionID
 *
 * @property Region $region
 * @property RoomCV[] $roomCVs
 */
class Department extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Department';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'regionID'], 'required'],
            [['id', 'regionID'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['code'], 'string', 'max' => 3],
            [['regionID'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['regionID' => 'id']],
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
            'regionID' => Yii::t('app', 'Region ID'),
        ];
    }

    /**
     * Get departments list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return Country[]
     */
    public static function getList()
    {
        $query = self::find();

        $query->orderBy(['id' => SORT_ASC]);

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
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'regionID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomCVs()
    {
        return $this->hasMany(RoomCV::className(), ['departmentID' => 'id']);
    }
}
