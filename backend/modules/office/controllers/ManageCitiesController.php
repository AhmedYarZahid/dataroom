<?php

namespace backend\modules\office\controllers;

use Yii;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use backend\modules\office\models\OfficeCity;
use backend\modules\office\models\OfficeCitySearch;

class ManageCitiesController extends \backend\controllers\Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'Cities');
        $this->titleSmall = Yii::t('admin', 'Manage cities');

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new OfficeCitySearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $markers = OfficeCity::getMarkers();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'markers' => $markers,
        ]);
    }

    public function actionCreate()
    {
        $model = new OfficeCity;
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'City has been created successfully.'));

                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'City has been updated successfully.'));

                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->delete();

        return $this->redirect(['index']);
    }

    public function actionUpdateMap()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = Yii::$app->request->post();

        $markers = [];
        $newMarkers = [];
        $ids = [];

        foreach ($data['markers'] as $value) {
            if (is_numeric($value['id'])) {
                $ids[] = $value['id'];
                $markers[$value['id']] = $value;
            } else {
                $newMarkers[] = $value;
            }
        }
        
        // Update existing cities
        $models = OfficeCity::findAll(['id' => $ids]);
        foreach ($models as $model) {
            $marker = $markers[$model->id];

            $model->name = $marker['name'];
            $model->mapData = [
                'top' => $marker['top'],
                'left' => $marker['left'],
                'labelTop' => $marker['labelTop'],
                'labelLeft' => $marker['labelLeft'],
            ];

            $model->save();
        }

        // Create new cities
        foreach ($newMarkers as $marker) {
            $model = new OfficeCity;

            $model->name = $marker['name'];
            $model->mapData = [
                'top' => $marker['top'],
                'left' => $marker['left'],
                'labelTop' => $marker['labelTop'],
                'labelLeft' => $marker['labelLeft'],
            ];

            $model->save();
        }

        return ['status' => 'ok'];
    }

    protected function findModel($id)
    {
        $query = OfficeCity::find();
        $query->where(['id' => $id]);

        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}