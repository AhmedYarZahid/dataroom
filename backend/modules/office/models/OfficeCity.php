<?php

namespace backend\modules\office\models;

use Yii;
use yii\db\ActiveRecord;

class OfficeCity extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'OfficeCity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['mapData'], 'safe'],
            [['isActive'], 'integer'],
            [['createdDate'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('admin', 'Title'),
            'mapData' => Yii::t('admin', 'Map Data'),
            'isActive' => Yii::t('app', 'Is Active'),
            'createdDate' => Yii::t('app', 'Created Date'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffices()
    {
        return $this->hasMany(Office::className(), ['cityID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveOffices()
    {
        return $this->hasMany(Office::className(), ['cityID' => 'id'])
            ->andWhere(['Office.isActive' => 1])
            ->with('activeMembers');
    }

    public function afterFind()
    {
        parent::afterFind();
        
        $this->mapData = unserialize($this->mapData);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (is_array($this->mapData)) {
            $this->mapData = serialize($this->mapData);
        }

        return true;
    }

    public static function getMarkers()
    {
        $models = self::findAll(['isActive' => 1]);

        $list = [];

        foreach ($models as $model) {
            $list[] = [
                'id' => $model->id,
                'name' => $model->name,
                'top' => $model->mapData ? $model->mapData['top'] : '50',
                'left' => $model->mapData ? $model->mapData['left'] : '50',
                'labelTop' => $model->mapData ? $model->mapData['labelTop'] : '',
                'labelLeft' => $model->mapData ? $model->mapData['labelLeft'] : '',
            ];
        }

        return $list;
    }
}