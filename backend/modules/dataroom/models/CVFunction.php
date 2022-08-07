<?php

namespace backend\modules\dataroom\models;

use Yii;

/**
 * This is the model class for table "CVFunction".
 *
 * @property integer $id
 * @property integer $parentID
 * @property string $name
 *
 * @property CVFunction $parent
 * @property CVFunction[] $subFunctions
 * @property RoomCV[] $roomCVs
 * @property RoomCV[] $roomCVs0
 */
class CVFunction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'CVFunction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parentID'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['parentID'], 'exist', 'skipOnError' => true, 'targetClass' => CVFunction::className(), 'targetAttribute' => ['parentID' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parentID' => Yii::t('app', 'Parent ID'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * Get functions list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param int|bool $parentID
     * @return CVFunction[]
     */
    public static function getList($parentID = false)
    {
        $query = self::find();

        if ($parentID !== false) {
            $query->andWhere(['parentID' => $parentID]);
        }

        $query->orderBy(['id' => SORT_ASC]);

        return $query->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(CVFunction::className(), ['id' => 'parentID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubFunctions()
    {
        return $this->hasMany(CVFunction::className(), ['parentID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomCVs()
    {
        return $this->hasMany(RoomCV::className(), ['functionID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomCVs0()
    {
        return $this->hasMany(RoomCV::className(), ['subFunctionID' => 'id']);
    }
}
