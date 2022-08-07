<?php

namespace backend\modules\office\models;

use Yii;
use yii\db\ActiveRecord;
use common\helpers\ArrayHelper;

class Office extends ActiveRecord
{
    public $address;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Office';
    }

     /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'cityID'], 'required'],
            [['body'], 'string'],
            [['isActive', 'cityID', 'userID'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['createdDate'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['cityID'], 'exist', 'skipOnError' => true, 'targetClass' => OfficeCity::className(), 'targetAttribute' => ['cityID' => 'id']],
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
            'body' => Yii::t('admin', 'Body'),
            'isActive' => Yii::t('app', 'Is Active'),
            'cityID' => Yii::t('admin', 'City'),
            'userID' => Yii::t('admin', 'User'),
            'createdDate' => Yii::t('app', 'Created Date'),
            'address' => Yii::t('app', 'Address'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(OfficeCity::className(), ['id' => 'cityID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveMembers()
    {
        return $this->hasMany(OfficeMember::className(), ['id' => 'officeMemberID'])
            ->viaTable('OfficeMember2Office', ['officeID' => 'id'])
            ->andWhere(['OfficeMember.isActive' => 1]);
    }

    public function cityList()
    {
        $models = OfficeCity::findAll(['isActive' => 1]);

        return ArrayHelper::map($models, 'id', 'name');
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (!$this->userID) {
            $this->userID = Yii::$app->user->id;
        }

        return true;
    }
}