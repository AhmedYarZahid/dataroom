<?php

namespace common\validators;

use Yii;
use common\helpers\ArrayHelper;
use common\models\City;
use yii\validators\Validator;

class CityValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $model->zip = trim($model->zip);

        if (!($cityList = City::getCitiesByZip($model->zip)) && strlen($model->zip) == 5) {
            $cityList = City::getCitiesByZip(substr($model->zip, 0, -1));
        }
        $cityIDs = ArrayHelper::map($cityList, 'id', 'id');

        if (!in_array($model->cityID, $cityIDs)) {
            $this->addError($model, 'cityID', Yii::t('admin', "City cannot be blank. Please provide correct zip to get list of cities."));
        } else {
            if (array_key_exists('departmentID', $model->attributes) || array_key_exists('countryID', $model->attributes)) {
                foreach ($cityList as $city) {
                    if ($city->id == $model->cityID) {
                        if (array_key_exists('departmentID', $model->attributes)) {
                            $model->departmentID = $city->departmentID;
                        }

                        if (array_key_exists('countryID', $model->attributes)) {
                            $model->countryID = $city->countryID;
                        }

                        break;
                    }
                }
            }
        }
    }
}