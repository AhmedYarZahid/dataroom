<?php

namespace backend\modules\dataroom\models;

use Yii;

/**
 * This is the model class for table "CVActivityDomain".
 *
 * @property integer $id
 * @property string $name
 *
 * @property RoomCV[] $roomCVs
 */
class CVActivityDomain extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'CVActivityDomain';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
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
     * Get activity domains list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return self[]
     */
    public static function getList()
    {
        $query = self::find();

        $query->orderBy(['id' => SORT_ASC]);

        return $query->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomCVs()
    {
        return $this->hasMany(RoomCV::className(), ['activityDomainID' => 'id']);
    }
}
