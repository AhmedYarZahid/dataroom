<?php

namespace backend\modules\notify\controllers;

use backend\modules\notify\models\Notify;
use backend\modules\notify\models\NotifySearch;
use common\helpers\FileHelper;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class ManageController extends \backend\controllers\Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'Notifications');
        $this->titleSmall = Yii::t('admin', 'Manage email notifications');

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

    public function actionIndex()
    {

        $searchModel = new NotifySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Notify model.
     *
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new Notify();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'New notification has been created successfully.'));

            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Notify model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id, true);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Notification has been updated successfully.'));

            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Notify model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Notify model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param bool $multilingual
     * @return Notify the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id, $multilingual = false)
    {
        $query = Notify::find();
        $query->where(['id' => $id]);

        if ($multilingual) {
            $query->multilingual();
        }

        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Upload image using Imperavi redactor
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function actionUploadImage()
    {
        $directory = Yii::getAlias('@uploads/editor/');
        $file = md5(date('YmdHis')) . '.' . pathinfo(@$_FILES['file']['name'], PATHINFO_EXTENSION);

        $array = [];
        if (move_uploaded_file(@$_FILES['file']['tmp_name'], $directory . $file)) {
            $array = ['url' => Yii::getAlias('@uploads/editor-rel/') . $file];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $array;
    }

    /**
     * Get images list for Imperavi redactor
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function actionGetImages()
    {
        $imagesList = FileHelper::findFiles(Yii::getAlias('@uploads/editor'));

        $result = array();
        foreach ($imagesList as $image) {
            $result[] = array(
                'thumb' => str_replace(Yii::getAlias('@uploads-webroot'), '', $image),
                'url' => str_replace(Yii::getAlias('@uploads-webroot'), '', $image),
                //'title' => 'Title1', // optional
                //'folder' => 'myFolder' // optional
            );
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $result;
    }
}
