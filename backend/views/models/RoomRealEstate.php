<?php

namespace backend\modules\dataroom\models;

use backend\modules\document\models\Document;
use common\components\DocumentBehavior;
use common\models\Country;
use common\models\Region;
use backend\modules\dataroom\Module;
use voskobovich\behaviors\ManyToManyBehavior;
use Yii;

/**
 * This is the model class for table "RoomRealEstate".
 *
 * @property integer $id
 * @property integer $roomID
 * @property string $mission
 * @property string $marketing
 * @property string $status
 * @property integer $propertyType
 * @property integer $propertySubType
 * @property string $libAd
 * @property string $address
 * @property string $zip
 * @property string $city
 * @property integer $countryID
 * @property integer $regionID
 * @property string $latitude
 * @property string $longitude
 * @property integer $constructionYear
 * @property integer $totalFloorsNumber
 * @property integer $floorNumber
 * @property double $area
 * @property integer $isDuplex
 * @property integer $isElevator
 * @property integer $roomsNumber
 * @property integer $bedroomsNumber
 * @property integer $bathroomsNumber
 * @property integer $showerRoomsNumber
 * @property integer $kitchensNumber
 * @property integer $toiletsNumber
 * @property integer $isSeparateToilet
 * @property integer $separateToiletsNumber
 * @property string $heatingType
 * @property integer $heatingEnergy
 * @property string $proximity
 * @property string $quickDescription
 * @property string $detailedDescription
 * @property string $keywords
 * @property string $sellingPrice
 * @property string $totalPrice
 * @property integer $totalPriceFrequency
 * @property string $charges
 * @property integer $chargesFrequency
 * @property string $currency
 * @property string $propertyTax
 * @property string $housingTax
 * @property integer $condominiumLotsNumber
 * @property integer $adLotNumber
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
 * @property Region $region
 * @property Country $country
 * @property RoomRealEstate2Cupboard[] $roomRealEstate2Cupboards
 * @property RoomCupboard[] $cupboards
 * @property RoomRealEstate2Facility[] $roomRealEstate2Facilities
 * @property RoomFacility[] $facilities
 * @property RoomRealEstate2Orientation[] $roomRealEstate2Orientations
 * @property RoomOrientation[] $orientations
 * @property RoomRealEstate2RoomType[] $roomRealEstate2RoomTypes
 * @property RoomType[] $roomTypes
 */
class RoomRealEstate extends AbstractDetailedRoom
{
    const MARKETING_SALE = 'sale';
    const MARKETING_RENT = 'rent';

    const PROPERTY_STATUS_FREE = 'free';
    const PROPERTY_STATUS_OCCUPIED = 'occupied';

    const PROPERTY_TYPE_APARTMENT = 1;
    const PROPERTY_TYPE_OFFICE = 2;
    const PROPERTY_TYPE_BUILDING = 3;
    const PROPERTY_TYPE_HOUSE = 4;
    const PROPERTY_TYPE_PARKING = 5;
    const PROPERTY_TYPE_GROUND = 6;

    const PROPERTY_SUB_TYPE_ECONOMIC = 1;
    const PROPERTY_SUB_TYPE_STANDARD = 2;

    const HEATING_TYPE_COLLECTIVE = 'collective';
    const HEATING_TYPE_INDIVIDUAL = 'individual';

    const HEATING_ENERGY_ELECTRIC = 1;
    const HEATING_ENERGY_FUEL = 2;
    const HEATING_ENERGY_GAS = 3;
    const HEATING_ENERGY_GROUND_GAS = 4;
    const HEATING_ENERGY_RADIATOR = 5;
    const HEATING_ENERGY_GROUND = 6;

    const PAYMENT_FREQUENCY_MONTHLY = 1;
    const PAYMENT_FREQUENCY_BIMONTHLY = 2;
    const PAYMENT_FREQUENCY_QUARTERLY = 3;
    const PAYMENT_FREQUENCY_FOUR_MONTHLY = 4;
    const PAYMENT_FREQUENCY_HALF_YEARLY = 5;
    const PAYMENT_FREQUENCY_ANNUAL = 6;

    const CURRENCY_EUR = 'eur';
    const CURRENCY_USD = 'usd';

    const AD_POSITION_FIRST = 1;
    const AD_POSITION_AFTER_ANNOUNCEMENT = 2;

    const PROCEDURE_LEGAL_REDRESS = 1;
    const PROCEDURE_JUDICIAL_LIQUIDATION = 2;
    const PROCEDURE_COMPANY_PROVISIONAL_ADMINISTRATION = 3;
    const PROCEDURE_COOWNERSHIP_PROVISIONAL_ADMINISTRATION = 4;
    const PROCEDURE_SUCCESSION_PROVISIONAL_ADMINISTRATION = 5;
    const PROCEDURE_INDIVISION_PROVISIONAL_ADMINISTRATION = 6;
    const PROCEDURE_COMPANIES_AMICABLE_LIQUIDATION = 7;
    const PROCEDURE_SAFEGUARD = 8;
    const PROCEDURE_COMMISSIONER_OFFICE = 9;
    const PROCEDURE_AD_HOC_MANDATE = 10;

    protected $fileFields = ['ca'];

    public function getDataroomSection()
    {
        return Module::SECTION_REAL_ESTATE;
    }

    public function getDataroomSectionLabel()
    {
        return 'AJAimmo';
    }

    public function getUrl($viaLogin = true)
    {
        if ($viaLogin) {
            return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/dataroom/user/login', 'goToRoomID' => $this->roomID]);
        } else {
            return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/dataroom/real-estate/view-room', 'id' => $this->id]);
        }
    }

    public function getUrlBackend()
    {
        return Yii::$app->urlManagerBackend->createAbsoluteUrl(['/dataroom/realestate/room/update/', 'id' => $this->id]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'RoomRealEstate';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $fileAttributes = array_fill_keys($this->fileFields, Document::TYPE_ROOM_SPECIFIC);

        return [
            [
                'class' => DocumentBehavior::class,
                'attributes' => $fileAttributes,
            ],
            'ManyToManyBehavior' => [
                'class' => ManyToManyBehavior::class,
                'relations' => [
                    'facilityIDs' => 'facilities',
                    'cupboardIDs' => 'cupboards',
                    'roomTypeIDs' => 'roomTypes',
                    'orientationIDs' => 'orientations',
                ],
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
            $scenarios[self::SCENARIO_UPDATE_FRONT] = ['propertyType', 'propertySubType', 'libAd',
                'address', 'zip', 'city', 'latitude', 'longitude', 'countryID',
                'regionID', 'constructionYear', 'totalFloorsNumber', 'floorNumber', 'area', 'isDuplex', 'isElevator',
                'roomsNumber', 'bedroomsNumber', 'bathroomsNumber', 'showerRoomsNumber', 'kitchensNumber', 'toiletsNumber',
                'isSeparateToilet', 'separateToiletsNumber', 'heatingType', 'heatingEnergy', 'facilityIDs', 'cupboardIDs',
                'roomTypeIDs', 'orientationIDs', 'proximity', 'quickDescription', 'detailedDescription', 'keywords',
                'condominiumLotsNumber', 'adLotNumber'
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
            [['ca', 'mission', 'propertyType', 'propertySubType', 'libAd', 'zip', 'city', 'countryID', 'regionID', 'area', 'procedure', 'procedureContact', 'email', 'availabilityDate'], 'required'],
            [['roomID', 'countryID', 'regionID', 'constructionYear', 'totalFloorsNumber', 'floorNumber', 'condominiumLotsNumber', 'adLotNumber', 'adPosition'], 'integer'],
            [['marketing', 'status', 'heatingType', 'quickDescription', 'detailedDescription', 'keywords', 'currency', 'procedureContact', 'offerAcceptanceCondition'], 'string'],
            [['latitude', 'longitude', 'area', 'sellingPrice', 'totalPrice', 'charges', 'propertyTax', 'housingTax'], 'number'],
            [['availabilityDate', 'presenceEndDate'], 'safe'],
            [['mission', 'libAd', 'proximity'], 'string', 'max' => 250],
            [['propertyType', 'propertySubType', 'totalPriceFrequency', 'chargesFrequency', 'procedure'], 'string', 'max' => 4],
            [['address', 'email'], 'string', 'max' => 150],
            [['zip'], 'string', 'length' => 5],
            [['city', 'firstName', 'lastName'], 'string', 'max' => 70],
            [['isDuplex', 'isElevator', 'isSeparateToilet', 'homePresence', 'visibility', 'individualAssetsPresence'], 'string', 'max' => 1],
            [['roomsNumber', 'bedroomsNumber', 'bathroomsNumber', 'showerRoomsNumber', 'kitchensNumber', 'toiletsNumber', 'separateToiletsNumber', 'heatingEnergy'], 'string', 'max' => 3],
            [['fax'], 'string', 'max' => 20],
            [['phone', 'phoneMobile'], 'string', 'length' => 10],
            [['roomID'], 'exist', 'skipOnError' => true, 'targetClass' => Room::class, 'targetAttribute' => ['roomID' => 'id']],
            [['regionID'], 'exist', 'skipOnError' => true, 'targetClass' => Region::class, 'targetAttribute' => ['regionID' => 'id']],
            [['countryID'], 'exist', 'skipOnError' => true, 'targetClass' => Country::class, 'targetAttribute' => ['countryID' => 'id']],

            [['email'], 'email'],
            [['facilityIDs', 'cupboardIDs', 'roomTypeIDs', 'orientationIDs'], 'each', 'rule' => ['integer']],

            [['heatingType', 'heatingEnergy', 'currency', 'marketing', 'status'], 'default', 'value' => null]
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
            'mission' => Yii::t('app', 'Mission'),
            'marketing' => Yii::t('app', 'Marketing'),
            'status' => Yii::t('app', 'Status'),
            'propertyType' => Yii::t('app', 'Property Type'),
            'propertySubType' => Yii::t('app', 'Property Sub Type'),
            'libAd' => Yii::t('app', 'Lib Ad'),
            'address' => Yii::t('app', 'Address'),
            'zip' => Yii::t('app', 'Zip'),
            'city' => Yii::t('app', 'City'),
            'countryID' => Yii::t('app', 'Country ID'),
            'regionID' => Yii::t('app', 'Region ID'),
            'latitude' => Yii::t('app', 'Latitude'),
            'longitude' => Yii::t('app', 'Longitude'),
            'constructionYear' => Yii::t('app', 'Construction Year'),
            'totalFloorsNumber' => Yii::t('app', 'Total Floors Number'),
            'floorNumber' => Yii::t('app', 'Floor Number'),
            'area' => Yii::t('app', 'Area'),
            'isDuplex' => Yii::t('app', 'Is Duplex'),
            'isElevator' => Yii::t('app', 'Is Elevator'),
            'roomsNumber' => Yii::t('app', 'Rooms Number'),
            'bedroomsNumber' => Yii::t('app', 'Bedrooms Number'),
            'bathroomsNumber' => Yii::t('app', 'Bathrooms Number'),
            'showerRoomsNumber' => Yii::t('app', 'Shower Rooms Number'),
            'kitchensNumber' => Yii::t('app', 'Kitchens Number'),
            'toiletsNumber' => Yii::t('app', 'Toilets Number'),
            'isSeparateToilet' => Yii::t('app', 'Is Separate Toilet'),
            'separateToiletsNumber' => Yii::t('app', 'Separate Toilets Number'),
            'heatingType' => Yii::t('app', 'Heating Type'),
            'heatingEnergy' => Yii::t('app', 'Heating Energy'),
            'proximity' => Yii::t('app', 'Proximity'),
            'quickDescription' => Yii::t('app', 'Quick Description'),
            'detailedDescription' => Yii::t('app', 'Detailed Description'),
            'keywords' => Yii::t('app', 'Keywords'),
            'sellingPrice' => Yii::t('app', 'Selling Price'),
            'totalPrice' => Yii::t('app', 'Total Price'),
            'totalPriceFrequency' => Yii::t('app', 'Total Price Frequency'),
            'charges' => Yii::t('app', 'Charges'),
            'chargesFrequency' => Yii::t('app', 'Charges Frequency'),
            'currency' => Yii::t('app', 'Currency'),
            'propertyTax' => Yii::t('app', 'Property Tax'),
            'housingTax' => Yii::t('app', 'Housing Tax'),
            'condominiumLotsNumber' => Yii::t('app', 'Condominium Lots Number'),
            'adLotNumber' => Yii::t('app', 'Ad Lot Number'),
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

            'facilityIDs' => Yii::t('app', 'Facilities'),
            'cupboardIDs' => Yii::t('app', 'Cupboards'),
            'roomTypeIDs' => Yii::t('app', 'Room types'),
            'orientationIDs' => Yii::t('app', 'Orientations'),
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
     * Get possible property sub-types
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getPropertySubTypes($exclude = [])
    {
        $types = [
            self::PROPERTY_SUB_TYPE_ECONOMIC => Yii::t('app', 'Economic'),
            self::PROPERTY_SUB_TYPE_STANDARD => Yii::t('app', 'Standard'),
        ];

        return array_diff_key($types, array_flip($exclude));
    }

    /**
     * Return property sub-type caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $type string
     *
     * @return string
     */
    public static function getPropertySubTypeCaption($type)
    {
        $types = self::getPropertySubTypes();

        return $types[$type];
    }

    /**
     * Get possible property statuses
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getStatuses($exclude = [])
    {
        $list = [
            self::PROPERTY_STATUS_FREE => Yii::t('app', 'Free'),
            self::PROPERTY_STATUS_OCCUPIED => Yii::t('app', 'Occupied'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return property status caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getStatusCaption($value)
    {
        $list = self::getStatuses();

        return isset($list[$value]) ? $list[$value] : null;
    }

    /**
     * Get possible marketings
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getMarketingList($exclude = [])
    {
        $list = [
            self::MARKETING_SALE => Yii::t('app', 'Sale'),
            self::MARKETING_RENT => Yii::t('app', 'Rent'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return property marketing caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getMarketingCaption($value)
    {
        $list = self::getMarketingList();

        return isset($list[$value]) ? $list[$value] : null;
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
     * Get possible payment frequency list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getPaymentFrequencyList($exclude = [])
    {
        $list = [
            self::PAYMENT_FREQUENCY_MONTHLY => Yii::t('app', 'Monthly'),
            self::PAYMENT_FREQUENCY_BIMONTHLY => Yii::t('app', 'Bimonthly'),
            self::PAYMENT_FREQUENCY_QUARTERLY => Yii::t('app', 'Quarterly'),
            self::PAYMENT_FREQUENCY_FOUR_MONTHLY => Yii::t('app', 'Four-monthly'),
            self::PAYMENT_FREQUENCY_HALF_YEARLY => Yii::t('app', 'Half-yearly'),
            self::PAYMENT_FREQUENCY_ANNUAL => Yii::t('app', 'Annual'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return payment frequency caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getPaymentFrequencyCaption($value)
    {
        $list = self::getPaymentFrequencyList();

        return isset($list[$value]) ? $list[$value] : null;
    }

    /**
     * Get possible currency list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getCurrencyList($exclude = [])
    {
        $list = [
            self::CURRENCY_EUR => Yii::t('app', 'Euro'),
            self::CURRENCY_USD => Yii::t('app', 'Dollar'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return currency caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getCurrencyCaption($value)
    {
        $list = self::getCurrencyList();

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
            self::PROCEDURE_LEGAL_REDRESS => Yii::t('app', 'Legal redress'),
            self::PROCEDURE_JUDICIAL_LIQUIDATION => Yii::t('app', 'Judicial liquidation'),
            self::PROCEDURE_COMPANY_PROVISIONAL_ADMINISTRATION => Yii::t('app', 'Provisional administration of the company'),
            self::PROCEDURE_COOWNERSHIP_PROVISIONAL_ADMINISTRATION => Yii::t('app', 'Provisional administration of co-ownership'),
            self::PROCEDURE_SUCCESSION_PROVISIONAL_ADMINISTRATION => Yii::t('app', 'Provisional Administration of Succession'),
            self::PROCEDURE_INDIVISION_PROVISIONAL_ADMINISTRATION => Yii::t('app', 'Provisional Administration of Indivision'),
            self::PROCEDURE_COMPANIES_AMICABLE_LIQUIDATION => Yii::t('app', 'Amicable liquidation of companies'),
            self::PROCEDURE_SAFEGUARD => Yii::t('app', 'Safeguard'),
            self::PROCEDURE_COMMISSIONER_OFFICE => Yii::t('app', 'Office of the Commissioner'),
            self::PROCEDURE_AD_HOC_MANDATE => Yii::t('app', 'Ad hoc mandate'),
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
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'regionID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'countryID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomRealEstate2Cupboards()
    {
        return $this->hasMany(RoomRealEstate2Cupboard::className(), ['roomRealEstateID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCupboards()
    {
        return $this->hasMany(RoomCupboard::className(), ['id' => 'cupboardID'])->viaTable('RoomRealEstate2Cupboard', ['roomRealEstateID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomRealEstate2Facilities()
    {
        return $this->hasMany(RoomRealEstate2Facility::className(), ['roomRealEstateID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacilities()
    {
        return $this->hasMany(RoomFacility::className(), ['id' => 'facilityID'])->viaTable('RoomRealEstate2Facility', ['roomRealEstateID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomRealEstate2Orientations()
    {
        return $this->hasMany(RoomRealEstate2Orientation::className(), ['roomRealEstateID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrientations()
    {
        return $this->hasMany(RoomOrientation::className(), ['id' => 'orientationID'])->viaTable('RoomRealEstate2Orientation', ['roomRealEstateID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomRealEstate2RoomTypes()
    {
        return $this->hasMany(RoomRealEstate2RoomType::className(), ['roomRealEstateID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomTypes()
    {
        return $this->hasMany(RoomType::className(), ['id' => 'roomTypeID'])->viaTable('RoomRealEstate2RoomType', ['roomRealEstateID' => 'id']);
    }
}
