<?php

namespace backend\modules\dataroom\models;

use common\models\Region;
use common\models\User;
use voskobovich\behaviors\ManyToManyBehavior;
use Yii;

/**
 * This is the model class for table "ProfileCoownership".
 *
 * @property integer $userID
 * @property integer $propertyType
 * @property integer $lotsNumber
 * @property integer $coownersNumber
 *
 * @property User $user
 * @property ProfileCoownership2Region[] $profileCoownership2Regions
 * @property Region[] $regions
 */
class ProfileCoownership extends AbstractProfile
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ProfileCoownership';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'ManyToManyBehavior' => [
                'class' => ManyToManyBehavior::class,
                'relations' => [
                    'regionIDs' => 'regions',
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userID', 'propertyType', 'lotsNumber'], 'required'],
            [['regionIDs'], 'required'],

            [['userID', 'lotsNumber', 'coownersNumber'], 'integer'],
            [['propertyType'], 'string', 'max' => 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userID' => Yii::t('app', 'User ID'),
            'propertyType' => Yii::t('app', 'Property Type'),
            'lotsNumber' => Yii::t('app', 'Lots Number'),
            'coownersNumber' => Yii::t('app', 'Coowners Number'),
            'regionIDs' => Yii::t('app', 'Regions list'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfileCoownership2Regions()
    {
        return $this->hasMany(ProfileCoownership2Region::className(), ['profileCoownershipID' => 'userID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegions()
    {
        return $this->hasMany(Region::className(), ['id' => 'regionID'])->viaTable('ProfileCoownership2Region', ['profileCoownershipID' => 'userID']);
    }
}
