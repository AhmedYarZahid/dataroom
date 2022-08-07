<?php

namespace backend\modules\parameter\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use \yii\db\Connection;
use yii\db\Expression;
use yii\helpers\Url;
use yii\base\Exception;
use yii\helpers\Html;
use yii\db\Query;

/**
 * This is the model class for table "Parameter".
 *
 * The followings are the available columns in table 'Parameter':
 * @property integer $id
 * @property string $name
 * @property string $value
 * @property string $description
 * @property string $type
 * @property string $group
 * @property string $updatedDate
 */
class Parameter extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Parameter';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => null,
                'updatedAtAttribute' => 'updatedDate',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value', 'type'], 'required'],
            [['name'], 'string', 'max' => 70],
            [['value'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 200],
            [['type'], 'string', 'max' => 7],
            [['group'], 'string', 'max' => 10],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('admin', 'ID'),
            'name' => Yii::t('admin', 'Name'),
            'value' => Yii::t('admin', 'Value'),
            'description' => Yii::t('admin', 'Description'),
            'type' => Yii::t('admin', 'Type'),
            'group' => Yii::t('admin', 'Group'),
            'updatedDate' => Yii::t('admin', 'Updated Date'),
        ];
    }

    /**
     * Get param value
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $paramName
     * @return string
     */
    public static function getParamValue($paramName)
    {
        return (new Query)->select(['value'])->from('Parameter')->where('name = :paramName', [':paramName' => $paramName])->scalar();
    }

    /**
     * Get params by group
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $paramsGroup
     * @return string
     */
    public static function getParamsByGroup($paramsGroup)
    {
        $query = Parameter::find();

        $result = $query->select(['name', 'value'])->where('`group` = :paramsGroup', [':paramsGroup' => $paramsGroup])->asArray()->all();

        $params = array();
        foreach ($result as $paramData) {
            $params[$paramData['name']] = $paramData['value'];
        }

        return $params;
    }

    /**
     * Get maximum amount for request
     *
     * @return integer
     */
    public static function getCVRoomPublishPeriod()
    {
        return self::getParamValue('CV_ROOM_PUBLISH_PERIOD');
    }

    /**
     * Get maximum amount for request
     *
     * @return integer
     */
    public static function getCVRoomExpirationPeriod()
    {
        return self::getParamValue('CV_ROOM_EXPIRATION_PERIOD');
    }

    /**
     * Get params related to CALCULATOR
     *
     * @return array
     */
    /*public static function getCalculatorParams()
    {
        return self::getParamsByGroup('CALCULATOR');
    }*/
}