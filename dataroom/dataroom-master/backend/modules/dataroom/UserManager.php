<?php

namespace backend\modules\dataroom;

use backend\modules\dataroom\models\ProfileCoownership;
use backend\modules\dataroom\models\ProfileCV;
use backend\modules\dataroom\models\ProfileRealEstate;
use Yii;
use common\models\User;
use backend\modules\dataroom\Module as DataroomModule;
use backend\modules\dataroom\models\AbstractProfile;
use backend\modules\dataroom\models\ProfileCompany;

class UserManager
{
    protected $profiles;

    /**
     * Returns array of available profiles (one profile per dataroom activity)
     *
     * @param User $user
     * @param bool $shouldHaveAccess
     * @return models\AbstractProfile[] Array of profiles
     */
    public function getAvailableProfiles(User $user, $shouldHaveAccess = true)
    {
        $profiles = [
            DataroomModule::SECTION_COMPANIES => $this->getProfile($user, DataroomModule::SECTION_COMPANIES),
            DataroomModule::SECTION_REAL_ESTATE => $this->getProfile($user, DataroomModule::SECTION_REAL_ESTATE),
            DataroomModule::SECTION_COOWNERSHIP  => $this->getProfile($user, DataroomModule::SECTION_COOWNERSHIP),
            DataroomModule::SECTION_CV  => $this->getProfile($user, DataroomModule::SECTION_CV),
        ];

        if ($shouldHaveAccess) {
            foreach ($profiles as $section => $profile) {
                if (!User::hasAnyAccessRequest($user->id, $section)) {
                    unset($profiles[$section]);
                }
            }
        }

        foreach ($profiles as $section => $profile) {
            $profile->load(Yii::$app->request->post());

            if (!in_array($section, $user->dataroomSections)) {
                $user->dataroomSections[] = $section;
            }
        }

        return $profiles;
    }

    /**
     * Saves a given user module and his activity-specific profiles.
     * 
     * @param  User         $user     
     * @param  array|string $sections Dataroom section codes.
     * @param  bool         $allowEmptyProfiles Whether to validate profiles input.
     * @return bool         Whether a user and profile model(s) were saved.
     */
    public function save(User $user, $sections = null, $allowEmptyProfiles = false)
    {
        //TODO: PROFILES ONLY FOR BUYERS!!!
        $profiles = $this->getAvailableProfiles($user);

        if ($sections === null) {
            $sections = $user->dataroomSections;
        } else if (!is_array($sections)) {
            $sections = [$sections];
        }
        
        $profilesToSave = $this->getProfilesToSave($profiles, $sections);
        
        if ($allowEmptyProfiles) {
            $profilesValid = true;
        } else {
            $profilesValid = $this->validateProfiles($profilesToSave);
        }

        if (empty($user->passwordHash) && $user->password !== null) {
            $user->setPassword($user->password);
        }

        if ($user->validate() && $profilesValid) {
            $user->save(false);
            
            foreach ($profilesToSave as $model) {
                $model->userID = $user->id;
                $model->save(false);
            }

            return true;
        }

        return false;
    }

    /**
     * @param  AbstractProfile[] $profiles     
     * @param  array|null $sections Dataroom section codes.
     * @return AbstractProfile[]
     */
    protected function getProfilesToSave($profiles, $sections)
    {
        if (is_array($sections)) {
            return array_filter($profiles, function($k) use ($sections) {
                return in_array($k, $sections);
            }, ARRAY_FILTER_USE_KEY);
        }

        return [];
    }

    /**
     * Validates given profiles.
     * 
     * @param  AbstractProfile[] $profiles
     * @return bool Whether validation has passed.
     */
    public function validateProfiles($profiles)
    {
        $valid = true;

        foreach ($profiles as $profile) {
            $valid = $profile->validate() && $valid;
        }

        return $valid;
    }

    /**
     * @param  User $user
     * @param  string $section One of dataroom sections.
     * @return mixed Profile model.
     */
    public function getProfile(User $user = null, $section)
    {
        if ($user && ($cached = $this->getCachedProfile($user, $section))) {
            return $cached;
        }

        switch ($section) {
            case DataroomModule::SECTION_COMPANIES:
                $profileClass = new ProfileCompany;
                break;

            case DataroomModule::SECTION_REAL_ESTATE:
                $profileClass = new ProfileRealEstate;
                break;

            case DataroomModule::SECTION_COOWNERSHIP:
                $profileClass = new ProfileCoownership;
                break;

            case DataroomModule::SECTION_CV:
                $profileClass = new ProfileCV;
                break;

            default:
                $profileClass = null;
                break;
        }

        if ($profileClass) {
            if ($user) {
                return $this->findOrCreateProfile($user, $profileClass);
            } else {
                return new $profileClass;
            }
        }

        return null;
    }

    /**
     * @param  User $user
     * @param  string $section One of dataroom sections.
     * @return mixed Profile model.
     */
    protected function getCachedProfile(User $user, $section)
    {
        return isset($this->profiles[$user->id][$section]) ? $this->profiles[$user->id][$section] : null;
    }

    /**
     * @param  User   $user
     * @param  string $profileClass
     * @return mixed  Profile model.
     */
    protected function findOrCreateProfile(User $user, $profileClass)
    {
        $profile = null;
        if ($user->id) {
            $profile = $profileClass::findOne($user->id);    
        }

        if (!$profile) {
            $profile = new $profileClass;
            $profile->userID = $user->id;
        }

        return $profile;
    }
}