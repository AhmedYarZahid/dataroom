<?php

namespace backend\modules\dataroom\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "ProfileCompany".
 *
 * @property integer $userID
 * @property string $targetedSector
 * @property string $targetedTurnover
 * @property string $entranceTicket
 * @property integer $geographicalArea
 * @property integer $targetAmount
 * @property string $effective
 *
 * @property User $user
 */
class ProfileCompany extends AbstractProfile
{
    const GEOGRAPHICAL_AREA_URBAN = 1;
    const GEOGRAPHICAL_AREA_PERI_URBAN = 2;
    const GEOGRAPHICAL_AREA_INDUSTRIAL = 3;
    const GEOGRAPHICAL_AREA_FRANK = 4;

    const TARGET_AMOUNT_500K_1M = 1;
    const TARGET_AMOUNT_1M_5M = 2;
    const TARGET_AMOUNT_5M_MORE = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ProfileCompany';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userID', 'geographicalArea', 'targetAmount', 'effective'], 'integer'],
            [['targetedSector', 'targetedTurnover', 'entranceTicket', 'geographicalArea', 'targetAmount'], 'required'],
            [['targetedSector', 'targetedTurnover', 'entranceTicket'], 'string', 'max' => 255],
            [['userID'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userID' => 'id']],

            //[['targetedSector', 'targetedTurnover', 'entranceTicket'], 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userID' => 'User ID',
            'targetedSector' => 'Secteur cible',
            'targetedTurnover' => 'Chiffre d’affaires ciblé',
            'entranceTicket' => "Ticket d'entrée",
            'geographicalArea' => Yii::t('app', 'Geographical Area'),
            'targetAmount' => Yii::t('app', 'Target Amount'),
            'effective' => Yii::t('app', 'Effective'),
        ];
    }

    public static function sectorList()
    {
        $arr = [
            "Agriculture",
            "BTP",
            "Commerce de détail",
            "Commerce de gros",
            "Immobilier",
            "Industrie",
            "Services et Transport",
        ];

        return array_combine($arr, $arr);
    }

    /**
     * @param $ist
     * @param $keys
     * @return array
     * get list values from keys using array list
     */
    public static function getListValues($ist, $keys)
    {
        $keys = explode(",", $keys);
        $values = [];
        foreach ($keys as $key) {
            $values[] = self::{$ist}()[$key];
        }
        return $values;
    }

    public static function turnoverList()
    {
        return [
            'less_1m' => "< 1M€",
            '1_5m' => "1 à 5M€",
            '5_50m' => "5 à 50M€",
            '50_100m' => "50 à 100M€",
            'more_100m' => "> 100M€",
        ];
    }

    public static function ticketList()
    {
        return [
            'less_1m' => "< 1M€",
            '1_5m' => "1 à 5M€",
            '5_50m' => "5 à 50M€",
            '50_100m' => "50 à 100M€",
            'more_100m' => "> 100M€",
        ];
    }

    /**
     * Get possible geographical areas
     *
     * @param array $exclude
     *
     * @return array
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     */
    public static function getGeographicalAreaList($exclude = [])
    {
        $list = [
            self::GEOGRAPHICAL_AREA_URBAN => Yii::t('app', 'Urban'),
            self::GEOGRAPHICAL_AREA_PERI_URBAN => Yii::t('app', 'Peri-Urban'),
            self::GEOGRAPHICAL_AREA_INDUSTRIAL => Yii::t('app', 'Industrial'),
            self::GEOGRAPHICAL_AREA_FRANK => Yii::t('app', 'Frank'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return geographical area caption
     *
     * @param $value string
     *
     * @return string
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     */
    public static function getGeographicalAreaCaption($value)
    {
        $list = self::getGeographicalAreaList();

        return isset($list[$value]) ? $list[$value] : null;
    }

    /**
     * Get possible target amount list
     *
     * @param array $exclude
     *
     * @return array
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     */
    public static function getTargetAmountList($exclude = [])
    {
        $list = [
            self::TARGET_AMOUNT_500K_1M => Yii::t('app', '500K€ to 1M€'),
            self::TARGET_AMOUNT_1M_5M => Yii::t('app', '1M€ to 5M€'),
            self::TARGET_AMOUNT_5M_MORE => Yii::t('app', '> 5M€'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return target amount caption
     *
     * @param $value string
     *
     * @return string
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     */
    public static function getTargetAmountCaption($value)
    {
        $list = self::getTargetAmountList();

        return isset($list[$value]) ? $list[$value] : null;
    }


    //func

    public function getTurnoverName()
    {
        return self::turnoverList()[$this->targetedTurnover];
    }

    public function getGeographicalAreaName()
    {
        return self::getGeographicalAreaCaption($this->geographicalArea);
    }


    public function getTargetAmountName()
    {
        return self::getTargetAmountCaption($this->targetAmount);
    }

    public function getTicket()
    {
        return self::ticketlist()[$this->entranceTicket];

    }
}