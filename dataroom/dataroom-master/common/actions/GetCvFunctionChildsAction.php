<?php

namespace common\actions;

use backend\modules\dataroom\models\CVFunction;
use common\helpers\ArrayHelper;
use Yii;
use yii\base\Action;
use yii\web\Response;

/* Get CV function childs by parent id (for dependent dropdowns) */

class GetCvFunctionChildsAction extends Action
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
    public function run($selected = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = '';
        if (($parentID = $_POST['depdrop_parents'][0]) && is_numeric($parentID)) {
            if ($childFunctions = CVFunction::find()->andWhere(['parentID' => $parentID])->all()) {
                $out = ArrayHelper::toArray($childFunctions, [CVFunction::class => ['id', 'name']]);
            }
        }

        return ['output' => $out, 'selected' => (string) $selected];
    }
}