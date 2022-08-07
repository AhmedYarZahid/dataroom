<?php

namespace app\modules\document\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use backend\modules\document\models\Document;
use backend\modules\document\models\DocumentSearch;
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
        $this->title = Yii::t('admin', 'Documents');
        $this->titleSmall = Yii::t('admin', 'Manage documents');

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
     * Lists all documents
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DocumentSearch();
        
        $queryParams = Yii::$app->request->queryParams;
        $queryParams['DocumentSearch']['type'] = Document::TYPE_REGULAR;
        
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Adds a new document(s)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function actionCreate()
    {
        $model = new Document;
        $model->scenario = 'add-documents';

        if ($model->load(Yii::$app->request->post())) {

            $model->type = Document::TYPE_REGULAR;
            $model->filePath = UploadedFile::getInstances($model, 'filePath');

            if ($model->validate()) {

                if (trim($model->title) === '') {
                    $model->title = trim($model->filePath[0]->baseName);
                }

                if (count($model->filePath) == 1) {
                    $model->saveUploadedDocument($model->filePath[0]);
                    $message = Yii::t('admin', 'Document has been created successfully.');
                } else {
                    $model->saveArchive($model->filePath);
                    $message = Yii::t('admin', 'Archive has been created successfully.');
                }

                if ($model->save(false)) {
                    \Yii::$app->getSession()->setFlash('success', $message);

                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates a particular document
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $id the ID of the document to be updated
     * @return Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';

        $oldFilePath = $model->filePath;

        if ($model->load(Yii::$app->request->post())) {

            $model->type = Document::TYPE_REGULAR;

            if ($model->validate()) {

                $documentUploaded = false;
                if ($filePaths = UploadedFile::getInstances($model, 'filePath')) {
                    if (count($filePaths) == 1) {
                        $documentUploaded = $model->saveUploadedDocument($filePaths[0]);
                    } else {
                        $documentUploaded = $model->saveArchive($filePaths);
                    }
                } else {
                    $model->filePath = $oldFilePath;
                }

                if ($model->save(false)) {

                    if ($documentUploaded) {
                        $model->setOldAttribute('filePath', $oldFilePath);
                        $model->removeOldDocument();
                    }

                    \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Document has been updated successfully.'));

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
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->type != Document::TYPE_REGULAR) {
            throw new BadRequestHttpException();
        }

        if ($model->delete()) {
            $model->setOldAttribute('filePath', $model->filePath);
            $model->removeOldDocument();
        }

        return $this->redirect(['index']);
    }

    /**
     * Downloads document
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $id
     * @throws NotFoundHttpException
     */
    public function actionDownload($id)
    {
        if (!$model = Document::find()->andWhere(['type' => Document::TYPE_REGULAR, 'id' => $id])->one()) {
            throw new NotFoundHttpException('Page not found.');
        }

        Yii::$app->response->sendFile($model->getDocumentPath(), $model->title . '.' . pathinfo($model->getDocumentPath(), PATHINFO_EXTENSION));
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Document the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Document::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
