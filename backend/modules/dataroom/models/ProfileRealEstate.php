<?php

namespace backend\modules\dataroom\models;

use common\models\Region;
use common\models\User;
use voskobovich\behaviors\ManyToManyBehavior;
use Yii;

/**
 * This is the model class for table "ProfileRealEstate".
 *
 * @property integer $userID
 * @property integer $targetSector
 * @property integer $targetedAssetsAmount
 * @property integer $assetsDestination
 * @property string $operationNature
 *
 * @property User $user
 * @property ProfileRealEstate2Region[] $profileRealEstate2Regions
 * @property Region[] $regions
 */
class ProfileRealEstate extends AbstractProfile
{
    const TARGET_SECTOR_URBAN_AREA = 1;
    const TARGET_SECTOR_PERI_URBAN_AREA = 2;
    const TARGET_SECTOR_INDUSTRIAL_AREA = 3;
    const TARGET_SECTOR_FREE_AREA = 4;

    const TARGETED_ASSETS_AMOUNT_500K = 1;
    const TARGETED_ASSETS_AMOUNT_500K_1M = 2;
    const TARGETED_ASSETS_AMOUNT_1M_5M = 3;
    const TARGETED_ASSETS_AMOUNT_5M = 4;

    const ASSETS_DESTINATION_COMMERCIAL = 1;
    const ASSETS_DESTINATION_DWELLING = 2;
    const ASSETS_DESTINATION_MIXED = 3;

    const OPERATION_NATURE_SALE = 'sale';
    const OPERATION_NATURE_RENT = 'rent';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ProfileRealEstate';
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
            [['userID', 'targetSector', 'targetedAssetsAmount', 'assetsDestination', 'operationNature'], 'required'],
            [['regionIDs'], 'required'],

            [['userID'], 'integer'],
            [['operationNature'], 'string'],
            [['targetSector', 'targetedAssetsAmount', 'assetsDestination'], 'string', 'max' => 3],

            ['operationNature', 'default', 'value' => null]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userID' => Yii::t('app', 'User ID'),
            'targetSector' => Yii::t('app', 'Target Sector'),
            'targetedAssetsAmount' => Yii::t('app', 'Targeted Assets Amount'),
            'assetsDestination' => Yii::t('app', 'Assets Destination'),
            'operationNature' => Yii::t('app', 'Operation Nature'),
            'regionIDs' => Yii::t('app', 'Regions list'),
        ];
    }

    /**
     * Get possible target sectors
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getTargetSectors($exclude = [])
    {
        $list = [
            self::TARGET_SECTOR_URBAN_AREA => Yii::t('app', 'Urban area'),
            self::TARGET_SECTOR_PERI_URBAN_AREA => Yii::t('app', 'Peri-urban area'),
            self::TARGET_SECTOR_INDUSTRIAL_AREA => Yii::t('app', 'Industrial area'),
            self::TARGET_SECTOR_FREE_AREA => Yii::t('app', 'Free area'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return target sector caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getTargetSectorCaption($value)
    {
        $list= self::getTargetSectors();

        return isset($list[$value]) ? $list[$value] : null;
    }

    /**
     * Get possible targeted assets amounts
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getTargetedAssetsAmountList($exclude = [])
    {
        $list = [
            self::TARGETED_ASSETS_AMOUNT_500K => Yii::t('app', '>500K€'),
            self::TARGETED_ASSETS_AMOUNT_500K_1M => Yii::t('app', '500K€ to 1M€'),
            self::TARGETED_ASSETS_AMOUNT_1M_5M => Yii::t('app', '1M€ to 5M€'),
            self::TARGETED_ASSETS_AMOUNT_5M => Yii::t('app', '>5M€'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return targeted assets amount caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getTargetedAssetsAmountCaption($value)
    {
        $list= self::getTargetedAssetsAmountList();

        return isset($list[$value]) ? $list[$value] : null;
    }

    /**
     * Get possible assets destinations
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getAssetsDestinationList($exclude = [])
    {
        $list = [
            self::ASSETS_DESTINATION_COMMERCIAL => Yii::t('app', 'Commercial'),
            self::ASSETS_DESTINATION_DWELLING => Yii::t('app', 'Dwelling'),
            self::ASSETS_DESTINATION_MIXED => Yii::t('app', 'Mixed'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return assets destination caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getAssetsDestinationCaption($value)
    {
        $list= self::getAssetsDestinationList();

        return isset($list[$value]) ? $list[$value] : null;
    }

    /**
     * Get possible operation natures
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getOperationNatureList($exclude = [])
    {
        $list = [
            self::OPERATION_NATURE_SALE => Yii::t('app', 'Sale'),
            self::OPERATION_NATURE_RENT => Yii::t('app', 'Rent'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return operation nature caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getOperationNatureCaption($value)
    {
        $list= self::getOperationNatureList();

        return isset($list[$value]) ? $list[$value] : null;
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
    public function getProfileRealEstate2Regions()
    {
        return $this->hasMany(ProfileRealEstate2Region::className(), ['profileRealEstateID' => 'userID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegions()
    {
        return $this->hasMany(Region::className(), ['id' => 'regionID'])->viaTable('ProfileRealEstate2Region', ['profileRealEstateID' => 'userID']);
    }
}
