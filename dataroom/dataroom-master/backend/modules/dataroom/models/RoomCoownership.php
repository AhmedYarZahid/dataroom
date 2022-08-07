<?php

namespace backend\modules\dataroom\models;

use backend\modules\dataroom\Module;
use backend\modules\document\models\Document;
use common\components\DocumentBehavior;
use common\models\Region;
use Yii;

/**
 * This is the model class for table "RoomCoownership".
 *
 * @property integer $id
 * @property integer $roomID
 * @property integer $propertyType
 * @property string $address
 * @property string $zip
 * @property string $city
 * @property integer $regionID
 * @property string $latitude
 * @property string $longitude
 * @property string $missionEndDate
 * @property string $coownershipName
 * @property integer $lotsNumber
 * @property integer $coownersNumber
 * @property integer $mainLotsNumber
 * @property integer $secondaryLotsNumber
 * @property integer $employeesNumber
 * @property integer $lastFinancialYearApprovedAccountsID
 * @property integer $constructionYear
 * @property integer $totalFloorsNumber
 * @property integer $isElevator
 * @property string $heatingType
 * @property integer $heatingEnergy
 * @property string $quickDescription
 * @property string $detailedDescription
 * @property string $keywords
 * @property integer $procedure
 * @property string $procedureContact
 * @property string $firstName
 * @property string $lastName
 * @property string $phone
 * @property string $fax
 * @property string $phoneMobile
 * @property string $email
 * @property string $availabilityDate
 * @property integer $homePresence
 * @property integer $visibility
 * @property string $offerAcceptanceCondition
 * @property integer $individualAssetsPresence
 * @property string $presenceEndDate
 * @property integer $adPosition
 *
 * @property Room $room
 * @property Document $lastFinancialYearApprovedAccounts
 * @property Region $region
 */
class RoomCoownership extends AbstractDetailedRoom
{
    const PROPERTY_TYPE_APARTMENT = 1;
    const PROPERTY_TYPE_OFFICE = 2;
    const PROPERTY_TYPE_BUILDING = 3;
    const PROPERTY_TYPE_HOUSE = 4;
    const PROPERTY_TYPE_PARKING = 5;
    const PROPERTY_TYPE_GROUND = 6;

    const HEATING_TYPE_COLLECTIVE = 'collective';
    const HEATING_TYPE_INDIVIDUAL = 'individual';

    const HEATING_ENERGY_ELECTRIC = 1;
    const HEATING_ENERGY_FUEL = 2;
    const HEATING_ENERGY_GAS = 3;
    const HEATING_ENERGY_GROUND_GAS = 4;
    const HEATING_ENERGY_RADIATOR = 5;
    const HEATING_ENERGY_GROUND = 6;

    const AD_POSITION_FIRST = 1;
    const AD_POSITION_AFTER_ANNOUNCEMENT = 2;

    const PROCEDURE_ARTICLE_29_1 = 1;
    const PROCEDURE_ARTICLE_29_1_POST_ALUR = 2;
    const PROCEDURE_ARTICLE_47 = 3;
    const PROCEDURE_ARTICLE_46 = 4;
    const PROCEDURE_PROVISIONAL_ADMINISTRATOR = 5;

    protected $fileFields = ['ca', 'lastFinancialYearApprovedAccountsID'];

    public function getDataroomSection()
    {
        return Module::SECTION_COOWNERSHIP;
    }

    public function getDataroomSectionLabel()
    {
        return 'AJAsyndic';
    }

    public function getUrl($viaLogin = true)
    {
        if ($viaLogin) {
            return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/dataroom/user/login', 'goToRoomID' => $this->roomID]);
        } else {
            return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/dataroom/coownership/view-room', 'id' => $this->id]);
        }
    }

    public function getUrlBackend()
    {
        return Yii::$app->urlManagerBackend->createAbsoluteUrl(['/dataroom/coownership/room/update/', 'id' => $this->id]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'RoomCoownership';
    }

    /**
     * @inheritdoc
     */
    function behaviors()
    {
        $fileAttributes = array_fill_keys($this->fileFields, Document::TYPE_ROOM_SPECIFIC);

        return [
            [
                'class' => DocumentBehavior::class,
                'attributes' => $fileAttributes,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = $scenarios['default'];
        $scenarios[self::SCENARIO_UPDATE] = $scenarios['default'];

        if (Yii::$app->user->id && Yii::$app->user->identity->isAdmin()) {
            $scenarios[self::SCENARIO_UPDATE_FRONT] = $scenarios['default'];
        } else {
            $scenarios[self::SCENARIO_UPDATE_FRONT] = ['propertyType',
                'address', 'zip', 'city', 'latitude', 'longitude', 'regionID',
                'missionEndDate', 'coownershipName', 'lotsNumber', 'coownersNumber', 'mainLotsNumber', 'secondaryLotsNumber',
                'employeesNumber', 'lastFinancialYearApprovedAccountsID',
                'constructionYear', 'totalFloorsNumber', 'isElevator', 'heatingType', 'heatingEnergy',
                'quickDescription', 'detailedDescription', 'keywords',
            ];
        }

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ca', 'propertyType', 'address', 'zip', 'city', 'regionID', 'coownershipName', 'lotsNumber', 'employeesNumber', 'procedure', 'procedureContact', 'email', 'availabilityDate'], 'required'],
            [['roomID', 'regionID', 'lotsNumber', 'coownersNumber', 'mainLotsNumber', 'secondaryLotsNumber', 'employeesNumber', 'constructionYear', 'totalFloorsNumber', 'adPosition'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['missionEndDate', 'availabilityDate', 'presenceEndDate'], 'safe'],
            [['heatingType', 'quickDescription', 'detailedDescription', 'keywords', 'procedureContact', 'offerAcceptanceCondition'], 'string'],
            [['propertyType', 'procedure'], 'string', 'max' => 4],
            [['address', 'email'], 'string', 'max' => 150],
            [['zip'], 'string', 'length' => 5],
            [['city', 'coownershipName', 'firstName', 'lastName'], 'string', 'max' => 70],
            [['isElevator', 'homePresence', 'visibility', 'individualAssetsPresence'], 'string', 'max' => 1],
            [['heatingEnergy'], 'string', 'max' => 3],
            [['fax'], 'string', 'max' => 20],
            [['phone', 'phoneMobile'], 'string', 'length' => 10],
            [['roomID'], 'exist', 'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['roomID' => 'id']],
            [['regionID'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['regionID' => 'id']],

            [$this->fileFields, 'file', 'extensions' => ['pdf','doc','docx','txt','jpg','jpeg','gif','png']],

            [['email'], 'email'],

            [['heatingType', 'heatingEnergy'], 'default', 'value' => null]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Code Room',
            'roomID' => Yii::t('app', 'Room ID'),
            'ca' => 'Engagement de confidentialité',
            'propertyType' => Yii::t('app', 'Property Type'),
            'address' => Yii::t('app', 'Address'),
            'zip' => Yii::t('app', 'Zip'),
            'city' => Yii::t('app', 'City'),
            'regionID' => Yii::t('app', 'Region ID'),
            'latitude' => Yii::t('app', 'Latitude'),
            'longitude' => Yii::t('app', 'Longitude'),
            'missionEndDate' => Yii::t('app', 'Mission End Date'),
            'coownershipName' => Yii::t('app', 'Coownership Name'),
            'lotsNumber' => Yii::t('app', 'Lots Number'),
            'coownersNumber' => Yii::t('app', 'Coowners Number'),
            'mainLotsNumber' => Yii::t('app', 'Main Lots Number'),
            'secondaryLotsNumber' => Yii::t('app', 'Secondary Lots Number'),
            'employeesNumber' => Yii::t('app', 'Employees Number'),
            'lastFinancialYearApprovedAccountsID' => Yii::t('app', 'Last Financial Year Approved Accounts'),
            'constructionYear' => Yii::t('app', 'Construction Year'),
            'totalFloorsNumber' => Yii::t('app', 'Total Floors Number'),
            'isElevator' => Yii::t('app', 'Is Elevator'),
            'heatingType' => Yii::t('app', 'Heating Type'),
            'heatingEnergy' => Yii::t('app', 'Heating Energy'),
            'quickDescription' => Yii::t('app', 'Quick Description'),
            'detailedDescription' => Yii::t('app', 'Detailed Description'),
            'keywords' => Yii::t('app', 'Keywords'),
            'procedure' => Yii::t('app', 'Procedure'),
            'procedureContact' => Yii::t('app', 'Procedure Contact'),
            'firstName' => Yii::t('app', 'First Name'),
            'lastName' => Yii::t('app', 'Last Name'),
            'phone' => Yii::t('app', 'Phone'),
            'fax' => Yii::t('app', 'Fax'),
            'phoneMobile' => Yii::t('app', 'Phone Mobile'),
            'email' => Yii::t('app', 'Email'),
            'availabilityDate' => Yii::t('app', 'Availability Date'),
            'homePresence' => Yii::t('app', 'Home Presence'),
            'visibility' => Yii::t('app', 'Visibility'),
            'offerAcceptanceCondition' => Yii::t('app', 'Offer Acceptance Condition'),
            'individualAssetsPresence' => Yii::t('app', 'Individual Assets Presence'),
            'presenceEndDate' => Yii::t('app', 'Presence End Date'),
            'adPosition' => Yii::t('app', 'Ad Position'),
        ];
    }

    /**
     * Get possible property types
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getPropertyTypes($exclude = [])
    {
        $types = [
            self::PROPERTY_TYPE_APARTMENT => Yii::t('app', 'Apartment'),
            self::PROPERTY_TYPE_OFFICE => Yii::t('app', 'Office'),
            self::PROPERTY_TYPE_BUILDING => Yii::t('app', 'Building'),
            self::PROPERTY_TYPE_HOUSE => Yii::t('app', 'House'),
            self::PROPERTY_TYPE_PARKING => Yii::t('app', 'Parking'),
            self::PROPERTY_TYPE_GROUND => Yii::t('app', 'Ground'),
        ];

        return array_diff_key($types, array_flip($exclude));
    }

    /**
     * Return property type caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $type string
     *
     * @return string
     */
    public static function getPropertyTypeCaption($type)
    {
        $types = self::getPropertyTypes();

        return $types[$type];
    }

    /**
     * Get possible heating types
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getHeatingTypeList($exclude = [])
    {
        $list = [
            self::HEATING_TYPE_COLLECTIVE => Yii::t('app', 'Collective'),
            self::HEATING_TYPE_INDIVIDUAL => Yii::t('app', 'Individual'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return heating type caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getHeatingTypeCaption($value)
    {
        $list = self::getHeatingTypeList();

        return isset($list[$value]) ? $list[$value] : null;
    }

    /**
     * Get possible heating energy list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getHeatingEnergyList($exclude = [])
    {
        $list = [
            self::HEATING_ENERGY_ELECTRIC => Yii::t('app', 'Electric'),
            self::HEATING_ENERGY_FUEL => Yii::t('app', 'Fuel'),
            self::HEATING_ENERGY_GAS => Yii::t('app', 'Gas'),
            self::HEATING_ENERGY_GROUND_GAS => Yii::t('app', 'Ground gas'),
            self::HEATING_ENERGY_RADIATOR => Yii::t('app', 'Radiator'),
            self::HEATING_ENERGY_GROUND => Yii::t('app', 'Soil'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return heating energy caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getHeatingEnergyCaption($value)
    {
        $list = self::getHeatingEnergyList();

        return isset($list[$value]) ? $list[$value] : null;
    }

    /**
     * Get possible ad positions list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getAdPositionList($exclude = [])
    {
        $list = [
            self::AD_POSITION_FIRST => Yii::t('app', 'First'),
            self::AD_POSITION_AFTER_ANNOUNCEMENT => Yii::t('app', 'After the announcement'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return ad position caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getAdPositionCaption($value)
    {
        $list = self::getAdPositionList();

        return isset($list[$value]) ? $list[$value] : null;
    }

    /**
     * Get possible procedures
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getProcedures($exclude = [])
    {
        $list = [
            self::PROCEDURE_ARTICLE_29_1 => Yii::t('app', 'Article 29-1'),
            self::PROCEDURE_ARTICLE_29_1_POST_ALUR => Yii::t('app', 'Article 29-1 post ALUR'),
            self::PROCEDURE_ARTICLE_47 => Yii::t('app', 'Article 47'),
            self::PROCEDURE_ARTICLE_46 => Yii::t('app', 'Article 46'),
            self::PROCEDURE_PROVISIONAL_ADMINISTRATOR => Yii::t('app', 'Provisional Administrator'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return procedure caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getProcedureCaption($value)
    {
        $list = self::getProcedures();

        return isset($list[$value]) ? $list[$value] : null;
    }

    /**
     * Get region name (magic method)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getRegionName()
    {
        return $this->region ? $this->region->name : null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'roomID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastFinancialYearApprovedAccounts()
    {
        return $this->hasOne(Document::className(), ['id' => 'lastFinancialYearApprovedAccountsID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'regionID']);
    }
}
