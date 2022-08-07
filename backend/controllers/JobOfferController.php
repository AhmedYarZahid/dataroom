<?php

namespace backend\controllers;

use Yii;
use common\models\JobOffer;
use common\models\JobOfferSearch;
use backend\controllers\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * JobOfferController implements the CRUD actions for JobOffer model.
 */
class JobOfferController extends Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'Job Offers');
        $this->titleSmall = Yii::t('admin', 'Manage job offers');

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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all JobOffer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new JobOfferSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single JobOffer model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new JobOffer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new JobOffer();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing JobOffer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id, true);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing JobOffer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->isRemoved = 1;

        $model->save(false);

        return $this->redirect(['index']);
    }

    /**
     * Finds the JobOffer model based on its primary key value
     *
     * @param integer $id
     * @param bool $multilingual
     * @return JobOffer the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id, $multilingual = false)
    {
        $query = JobOffer::find();
        $query->where(['id' => $id, 'isRemoved' => 0]);

        if ($multilingual) {
            $query->multilingual();
        }

        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
