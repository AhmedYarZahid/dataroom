<?php

namespace backend\modules\mailing\models;

use common\models\UserQuery;
use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Newsletter;
use backend\modules\dataroom\models\ProfileCompany;
use yii\db\ActiveQuery;

class MailingContactForm extends Model
{
    const PROFILE_SUBSCRIBER = 'nl_subscriber';
    const PROFILE_MANAGER = 'manager';
    const PROFILE_USER = 'user';

    const ACTIVITY_COMPANIES = 'companies';
    const ACTIVITY_REAL_ESTATE = 'real_estate';
    const ACTIVITY_COOWNERSHIP = 'coownership';
    const ACTIVITY_CV = 'cv';

    public $profile;
    public $activity;

    // PROFILE_SUBSCRIBER
    public $profession;

    // ACTIVITY_COMPANIES
    public $targetedSector;
    public $targetedTurnover;
    public $entranceTicket;
    public $geographicalArea;
    public $targetAmount;
    public $effectiveMin;
    public $effectiveMax;

    // ACTIVITY_REAL_ESTATE
    public $targetSector;
    public $regionIDs;
    public $targetedAssetsAmount;
    public $assetsDestination;
    public $operationNature;

    // ACTIVITY_COOWNERSHIP
    public $propertyType;
    public $coownershipRegionIDs;
    public $lotsNumber;
    public $coownersNumber;

    public $contactIds;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['profile', 'activity',
                'profession',
                'targetedSector', 'targetedTurnover', 'entranceTicket', 'geographicalArea', 'targetAmount', 'effectiveMin', 'effectiveMax',
                'targetSector', 'regionIDs', 'targetedAssetsAmount', 'assetsDestination', 'operationNature',
                'propertyType', 'coownershipRegionIDs', 'lotsNumber', 'coownersNumber'
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'profile' => Yii::t('admin', 'Profile'),
            'activity' => Yii::t('admin', 'Activity'),

            'targetedSector' => 'Secteur cible',
            'targetedTurnover' => 'Chiffre d’affaires ciblé',
            'entranceTicket' => "Ticket d'entrée",
            'geographicalArea' => Yii::t('app', 'Geographical Area'),
            'targetAmount' => Yii::t('app', 'Target Amount'),

            'targetSector' => Yii::t('app', 'Target Sector'),
            'targetedAssetsAmount' => Yii::t('app', 'Targeted Assets Amount'),
            'assetsDestination' => Yii::t('app', 'Assets Destination'),
            'operationNature' => Yii::t('app', 'Operation Nature'),
            'regionIDs' => Yii::t('app', 'Regions list'),

            'propertyType' => Yii::t('app', 'Property Type'),
            'lotsNumber' => Yii::t('app', 'Lots Number'),
            'coownersNumber' => Yii::t('app', 'Coowners Number'),
            'coownershipRegionIDs' => Yii::t('app', 'Regions list'),
        ];
    }

    public function profileList($addEmptyItem = true)
    {
        $list = [
            self::PROFILE_SUBSCRIBER => Yii::t('app', 'Contact (site Aja)'),
            self::PROFILE_USER => Yii::t('app', 'Buyer'),
            self::PROFILE_MANAGER => Yii::t('app', 'Manager'),
        ];

        return $addEmptyItem ? array(null => '') + $list : $list;

    }

    /**
     * Get profiles list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return array
     */
    public static function getProfileList()
    {
        return (new self)->profileList(false);
    }

    /**
     * Get profile caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $value
     * @return null
     */
    public static function getProfileCaption($value)
    {
        $list = self::getProfileList();

        return isset($list[$value]) ? $list[$value] : null;
    }

    /**
     * Get possible activities
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $exclude
     *
     * @return array
     */
    public static function getActivityList($exclude = [])
    {
        $list = [
            self::ACTIVITY_COMPANIES => Yii::t('app', 'Companies'),
            self::ACTIVITY_REAL_ESTATE => Yii::t('app', 'Real Estate'),
            self::ACTIVITY_COOWNERSHIP => Yii::t('app', 'Coownership'),
            self::ACTIVITY_CV => Yii::t('app', 'CV'),
        ];

        return array_diff_key($list, array_flip($exclude));
    }

    /**
     * Return activity caption
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $value string
     *
     * @return string
     */
    public static function getActivityCaption($value)
    {
        $list = self::getActivityList();

        return $list[$value];
    }

    /**
     * Get professions list
     *
     * @return array
     */
    public function professionList()
    {
        $nl = new Newsletter;
        return [null => ''] + $nl->professionList();
    }

    public function sectorList()
    {
        return [null => ''] + ProfileCompany::sectorList();
    }

    public function turnoverList()
    {
        return [null => ''] + ProfileCompany::turnoverList();
    }

    public function ticketList()
    {
        return [null => ''] + ProfileCompany::ticketList();
    }

    /**
     * Get contacts list (for multiple selector)
     *
     * @return array
     */
    public function contactList()
    {
        $models = [];

        switch ($this->profile) {
            case self::PROFILE_MANAGER:
                $query = User::find()->isMailingContact()
                    ->ofType(User::TYPE_MANAGER);

                // No sense to use activity for managers
                //$this->applyActivityFilterQuery($query);

                $models = $query->orderBy('createdDate DESC')->all();

                break;

            case self::PROFILE_USER:
                $query = User::find()->isMailingContact()
                    ->ofType(User::TYPE_USER);

                $this->applyActivityFilterQuery($query);
                
                $models = $query->orderBy('createdDate DESC')->all();

                break;
            
            case self::PROFILE_SUBSCRIBER:
                $models = Newsletter::find()->andWhere(['isActive' => 1])->andFilterWhere([
                    'profession' => $this->profession,
                ])->orderBy('createdDate DESC')->all();
                break;
        }
        
        $selected = $this->contactIds ? $this->idsToModels($this->contactIds) : [];

        $models = array_merge($selected, $models);

        return $this->modelsToIds($models);
    }

    /**
     * Apply "activity" filter to query
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param UserQuery $query
     */
    protected function applyActivityFilterQuery(UserQuery $query)
    {
        switch ($this->activity) {
            case self::ACTIVITY_COMPANIES:
                $query->withProfile('profileCompany');

                $query->andFilterWhere([
                    'ProfileCompany.targetedSector' => $this->targetedSector,
                    'ProfileCompany.targetedTurnover' => $this->targetedTurnover,
                    'ProfileCompany.entranceTicket' => $this->entranceTicket,
                    'ProfileCompany.geographicalArea' => $this->geographicalArea,
                    'ProfileCompany.targetAmount' => $this->targetAmount,
                ]);

                if ($this->effectiveMin && $this->effectiveMax) {
                    $query->andFilterWhere(['and',
                        ['>=', 'ProfileCompany.effective', $this->effectiveMin],
                        ['<=', 'ProfileCompany.effective', $this->effectiveMax],
                    ]);
                } elseif ($this->effectiveMin) {
                    $query->andFilterWhere(
                        ['>=', 'ProfileCompany.effective', $this->effectiveMin]
                    );
                } elseif ($this->effectiveMax) {
                    $query->andFilterWhere(
                        ['<=', 'ProfileCompany.effective', $this->effectiveMax]
                    );
                }

                break;

            case self::ACTIVITY_REAL_ESTATE:
                if ($this->regionIDs) {
                    $query->innerJoinWith(['profileRealEstate' => function (ActiveQuery $query) {
                        $query->innerJoinWith(['regions' => function (ActiveQuery $query) {
                            $query->andOnCondition(['Region.id' => $this->regionIDs]);
                        }]);
                    }]);
                } else {
                    $query->innerJoinWith('profileRealEstate');
                }

                $query->andFilterWhere([
                    'ProfileRealEstate.targetSector' => $this->targetSector,
                    'ProfileRealEstate.targetedAssetsAmount' => $this->targetedAssetsAmount,
                    'ProfileRealEstate.assetsDestination' => $this->assetsDestination,
                    'ProfileRealEstate.operationNature' => $this->operationNature,
                ]);

                break;

            case self::ACTIVITY_COOWNERSHIP:
                if ($this->coownershipRegionIDs) {
                    $query->innerJoinWith(['profileCoownership' => function (ActiveQuery $query) {
                        $query->innerJoinWith(['regions' => function (ActiveQuery $query) {
                            $query->andOnCondition(['Region.id' => $this->coownershipRegionIDs]);
                        }]);
                    }]);
                } else {
                    $query->withProfile('profileCoownership');
                }

                $query->andFilterWhere([
                    'ProfileCoownership.propertyType' => $this->propertyType,
                ]);

                $query->andFilterWhere(['>=', 'ProfileCoownership.lotsNumber', $this->lotsNumber]);
                $query->andFilterWhere(['>=', 'ProfileCoownership.coownersNumber', $this->coownersNumber]);

                break;

            case self::ACTIVITY_CV:
                $query->withProfile('profileCV');

                break;
        }
    }

    protected function idsToModels($ids)
    {
        $userIds = [];
        $newsletterIds = [];

        foreach ($ids as $contactId) {
            if (strpos($contactId, 'user_') !== false) {
                $userIds[] = str_replace('user_', '', $contactId);
            } elseif (strpos($contactId, 'newsletter_') !== false) {
                $newsletterIds[] = str_replace('newsletter_', '', $contactId);
            }
        }

        $users = User::find()->where(['in', 'id', $userIds])->orderBy('createdDate DESC')->all();
        $newsletters = Newsletter::find()->where(['in', 'id', $newsletterIds])->orderBy('createdDate DESC')->all();

        return array_merge($users, $newsletters);
    }

    protected function modelsToIds($models)
    {
        $ids = [];

        foreach ($models as $model) {
            if ($model instanceof User) {
                $label = $model->email;
                if (trim($model->fullName)) {
                    $label .= ' (' . trim($model->fullName) . ')';
                }

                $ids['user_' . $model->id] = $label; //$model->email;
            } elseif ($model instanceof Newsletter) {
                $label = $model->email;
                if (trim($model->fullName)) {
                    $label .= ' (' . trim($model->fullName) . ')';
                }

                $ids['newsletter_' . $model->id] = $label; //$model->email;
            }
        }

        return $ids;
    }
}