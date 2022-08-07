<?php
namespace frontend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use frontend\controllers\Controller as FrontendController;
use backend\modules\office\models\Office;
use backend\modules\office\models\OfficeSearch;
use backend\modules\office\models\OfficeCity;
use backend\modules\office\models\OfficeCitySearch;

class OfficesController extends FrontendController
{
    public function actionIndex($cityID = null)
    {
        if (!$cityID) {
            $city = OfficeCity::find()->one();
        } else {
            $city = OfficeCity::findOne($cityID);    
        }

        $markers = OfficeCity::getMarkers();

        if (!$city) {
            throw new NotFoundHttpException;
        }

        return $this->render('index', [
            'markers' => $markers,
            'city' => $city,
        ]);
    }

    public function actionMap($id)
    {   
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Office::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException;
        }

        return [
            'status' => 'ok',
            'content' => $this->renderPartial('_map', [
                'model' => $model,
            ]),
        ];
    }
}
