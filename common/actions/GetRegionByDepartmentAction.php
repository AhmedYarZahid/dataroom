<?php

namespace common\actions;

use common\helpers\ArrayHelper;
use common\models\Region;
use Yii;
use yii\base\Action;
use yii\web\Response;

/* Get region by department (for dependent dropdowns) */

class GetRegionByDepartmentAction extends Action
{
    /**
     * @inheritdoc
     */
    public function init()
    {

    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = '';
        $selected = '';
        if (($departmentID = $_POST['depdrop_parents'][0]) && is_numeric($departmentID)) {
            if ($region = Region::find()->innerJoinWith('departments')->andWhere(['Department.id' => $departmentID])->one()) {
                $out = ArrayHelper::toArray([$region], [Region::class => ['id', 'name']]);
                $selected = $region->id;
            }
        }

        return ['output' => $out, 'selected' => (string) $selected];
    }
}