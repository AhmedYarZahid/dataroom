<?php

namespace backend\modules\trendypage\controllers;

use lateos\trendypage\controllers\ManageController as BaseManageController;
use lateos\trendypage\models\TrendyPage;
use yii\web\NotFoundHttpException;
use Yii;

class ManageController extends BaseManageController
{
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->id === 1) {
            throw new NotFoundHttpException('The requested page does not exist.');    
        }

        $model->isRemoved = 1;
        $model->save(false);

        return $this->redirect(['index']);
    }

    public function actionDeleteMultiple()
    {
        $pagesToDelete = Yii::$app->request->post('pagesToDelete');

        if (is_array($pagesToDelete)) {
            $models = TrendyPage::find()->where(['id' => $pagesToDelete])->all();
            foreach ($models as $model) {
                if ($model->id === 1) {
                    continue;
                }

                $model->isRemoved = 1;
                $model->save(false);
            }
        }

        return $this->redirect(['index']);
    }
}