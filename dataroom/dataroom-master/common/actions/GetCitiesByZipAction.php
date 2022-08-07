<?php

namespace common\actions;

use common\models\City;
use Yii;
use yii\base\Action;
use yii\web\Response;

class GetCitiesByZipAction extends Action
{
    public $zip;

    /**
     * @inheritdoc
     */
    public function init()
    {

    }

    /**
     * @inheritdoc
     */
    public function run($zip, $selected = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $zip = trim($zip);

        if (!($cityList = City::getCitiesByZip($zip)) && strlen($zip) == 5) {
            $cityList = City::getCitiesByZip(substr($zip, 0, -1));
        }

        $result = "<option value=''>" . Yii::t('app', '- City -') . "</option>";
        foreach ($cityList as $city) {
            $result .= "<option value='" . $city->id . "' " . ($city->id == $selected ? 'selected' : '') . ">" . $city->name . "</option>";
        }

        return $result;
    }
}