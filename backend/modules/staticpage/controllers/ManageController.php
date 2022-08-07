<?php

namespace app\modules\staticpage\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use backend\modules\staticpage\models\StaticPage;
use backend\modules\staticpage\models\StaticPageSearch;
use common\helpers\FileHelper;
use yii\filters\AccessControl;


class ManageController extends \backend\controllers\Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'Static Pages');
        $this->titleSmall = Yii::t('admin', 'Manage static pages');

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

    /**
     * Static pages list
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new StaticPageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new page
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StaticPage();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'New page has been created successfully.'));

            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing page
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Page has been updated successfully.'));

            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the StaticPage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StaticPage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StaticPage::findOne($id)) !== null) {
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