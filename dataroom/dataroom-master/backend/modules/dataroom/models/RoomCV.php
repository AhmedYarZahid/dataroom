<?php

namespace backend\modules\dataroom\models;

use backend\modules\dataroom\Module;
use backend\modules\document\models\Document;
use common\components\DocumentBehavior;
use common\models\Department;
use common\models\Region;
use Yii;

/**
 * This is the model class for table "RoomCV".
 *
 * @property integer $id
 * @property integer $roomID
 * @property string $companyName
 * @property integer $activityDomainID
 * @property string $candidateProfile
 * @property integer $functionID
 * @property integer $subFunctionID
 * @property string $firstName
 * @property string $lastName
 * @property string $address
 * @property string $email
 * @property string $phone
 * @property integer $cvID
 * @property integer $departmentID
 * @property integer $regionID
 * @property string $seniority
 * @property string $state
 *
 * @property CVActivityDomain $activityDomain
 * @property CVFunction $function
 * @property CVFunction $subFunction
 * @property Department $department
 * @property Document $cv
 * @property Region $region
 * @property Room $room
 */
class RoomCV extends AbstractDetailedRoom
{
    const STATE_TO_FILL = 'to_fill';
    const STATE_TO_CORRECT = 'to_correct';
    const STATE_READY = 'ready';

    public $roomsNumber = 1;

    protected $fileFields = ['ca', 'cvID'];

    public function getDataroomSection()
    {
        return Module::SECTION_CV;
    }

    public function getDataroomSectionLabel()
    {
        return 'AJAreclassement';
    }

    public function getUrl($viaLogin = true)
    {
        if ($viaLogin) {
            return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/dataroom/user/login', 'goToRoomID' => $this->roomID]);
        } else {
            return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/dataroom/cv/view-room/', 'id' => $this->id]);
            //return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/dataroom/cv/update-room/', 'id' => $this->id]);
        }
    }

    public function getUrlBackend()
    {
        return Yii::$app->urlManagerBackend->createAbsoluteUrl(['/dataroom/cv/room/update/', 'id' => $this->id]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'RoomCV';
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

        //$scenarios[self::SCENARIO_CREATE] = $scenarios['default'];
        $scenarios[self::SCENARIO_CREATE] = ['ca', 'roomsNumber'];
        $scenarios[self::SCENARIO_UPDATE] = $scenarios['default'];

        if (Yii::$app->user->id && Yii::$app->user->identity->isAdmin()) {
            $scenarios[self::SCENARIO_UPDATE_FRONT] = $scenarios['default'];
        } else {
            $scenarios[self::SCENARIO_UPDATE_FRONT] = ['companyName', 'activityDomainID', 'candidateProfile',
                'functionID', 'subFunctionID', 'firstName', 'lastName', 'address', 'email', 'phone', 'cvID',
                'departmentID', 'regionID', 'seniority'
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
            [['ca', 'companyName', 'activityDomainID', 'candidateProfile', 'functionID', 'firstName', 'lastName', 'address', 'email', 'phone', 'cvID', 'departmentID', 'regionID', 'seniority'], 'required'],

            [['subFunctionID'], 'required', 'when' => function (self $model) {
                return $model->functionID && !empty($model->function->subFunctions);
            }],

            [['roomID', 'activityDomainID', 'functionID', 'subFunctionID', 'departmentID', 'regionID'], 'integer'],
            [['candidateProfile', 'state'], 'string'],
            [['companyName'], 'string', 'max' => 70],
            [['firstName', 'lastName'], 'string', 'max' => 50],
            [['address', 'email', 'seniority'], 'string', 'max' => 150],
            [['phone'], 'string', 'length' => 10],
            [['email'], 'email'],

            [['roomsNumber'], 'required', 'on' => self::SCENARIO_CREATE],
            [['roomsNumber'], 'integer', 'min' => 1, 'on' => self::SCENARIO_CREATE],

            [$this->fileFields, 'file', 'extensions' => ['pdf']],
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
            'ca' => 'Engagement de confidentialité (PDF)',
            'companyName' => Yii::t('app', 'Company Name'),
            'activityDomainID' => Yii::t('app', 'Activity Domain'),
            'candidateProfile' => Yii::t('app', 'Candidate Profile'),
            'functionID' => Yii::t('app', 'Function'),
            'subFunctionID' => Yii::t('app', 'Sub Function'),
            'firstName' => Yii::t('app', 'First Name'),
            'lastName' => Yii::t('app', 'Last Name'),
            'address' => Yii::t('app', 'Address'),
            'email' => Yii::t('app', 'Email'),
            'phone' => Yii::t('app', 'Phone'),
            'cvID' => Yii::t('app', 'CV in PDF'),
            'departmentID' => Yii::t('app', 'Department'),
            'regionID' => Yii::t('app', 'Region'),
            'seniority' => Yii::t('app', 'Seniority'),

            'roomsNumber' => Yii::t('app', 'Rooms number'),
        ];
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
     * Get department name (magic method)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getDepartmentName()
    {
        return $this->department ? $this->department->name : null;
    }

    /**
     * Get function name
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getFunctionName()
    {
        return $this->function ? $this->function->name : null;
    }

    /**
     * Get sub-function name
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getSubFunctionName()
    {
        return $this->subFunction ? $this->subFunction->name : null;
    }

    /**
     * Get activity domain name
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getActivityDomainName()
    {
        return $this->activityDomain ? $this->activityDomain->name : null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivityDomain()
    {
        return $this->hasOne(CVActivityDomain::className(), ['id' => 'activityDomainID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFunction()
    {
        return $this->hasOne(CVFunction::className(), ['id' => 'functionID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubFunction()
    {
        return $this->hasOne(CVFunction::className(), ['id' => 'subFunctionID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::className(), ['id' => 'departmentID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCv()
    {
        return $this->hasOne(Document::className(), ['id' => 'cvID']);
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
    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'roomID']);
    }
}
