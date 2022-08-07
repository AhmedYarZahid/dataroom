<?php

namespace app\modules\news\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use backend\modules\news\models\News;
use backend\modules\news\models\NewsSearch;
use common\helpers\FileHelper;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

class ManageController extends \backend\controllers\Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'News');
        $this->titleSmall = Yii::t('admin', 'Manage news');

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
     * Lists all news
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Adds a news
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function actionCreate()
    {
        $model = new News;
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {

                $model->saveUploadedImage();

                if ($model->save(false)) {

                    \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'News has been created successfully.'));

                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates a particular news
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $id the ID of the news to be updated
     * @return Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id, true);
        $oldImage = $model->image;

        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {
                $imageUploaded = $model->saveUploadedImage();

                if ($model->save(false)) {

                    if ($imageUploaded) {
                        $model->setOldAttribute('image', $oldImage);
                        $model->removeOldImage();
                    }

                    \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'News has been updated successfully.'));

                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes a particular document
     *
     * @param integer $id the ID of the model to be deleted
     * @return Response
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete()) {
            $model->setOldAttribute('image', $model->image);
            $model->removeOldImage();
        }

        return $this->redirect(['index']);
    }

    /**
     * Upload image using Imperavi redactor
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function actionUploadImage()
    {
        $directory = Yii::getAlias('@uploads/news/');
        $file = md5(date('YmdHis')) . '.' . pathinfo(@$_FILES['file']['name'], PATHINFO_EXTENSION);

        $array = [];
        if (move_uploaded_file(@$_FILES['file']['tmp_name'], $directory . $file)) {
            $array = ['url' => Yii::getAlias('@uploads/news-rel/') . $file];
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
        $imagesList = FileHelper::findFiles(Yii::getAlias('@uploads/news'));

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

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param bool $multilingual
     * @return News the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id, $multilingual = false)
    {
        $query = News::find();
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
}
