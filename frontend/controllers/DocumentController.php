<?php

namespace frontend\controllers;

use backend\modules\document\models\Document;
use frontend\controllers\Controller as FrontendController;
use yii;
use yii\web\NotFoundHttpException;

class DocumentController extends FrontendController
{
    public function actionDownload($id)
    {
        if (!$model = Document::find()->andWhere(['type' => Document::TYPE_REGULAR, 'id' => $id])->one()) {
            throw new NotFoundHttpException('Page not found.');
        }
        Yii::$app->response->sendFile($model->getDocumentPath(), $model->title . '.' . pathinfo($model->getDocumentPath(), PATHINFO_EXTENSION));
    }
}