<?php

namespace backend\modules\contact\controllers;

use backend\modules\contact\models\ContactNotify;
use Yii;
use backend\modules\contact\models\ContactThread;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use backend\modules\contact\models\Contact;
use backend\modules\contact\models\ContactTemplate;
use backend\modules\contact\models\ContactSearch;
use yii\filters\AccessControl;
use common\helpers\FileHelper;


class ManageController extends \backend\controllers\Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'Contact');
        $this->titleSmall = Yii::t('admin', 'Manage conversations between users and administrator');

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
     * Contacts list
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ContactSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * View conversation thread / Reply to user
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $answerModel = new ContactThread();
        $templateModel = new ContactTemplate();

        if ($answerModel->load(Yii::$app->request->post()) && !$model->isClosed) {

            $answerModel->contactID = $model->id;
            $answerModel->sender = ContactThread::SENDER_ADMIN;
            $answerModel->createdDate = date('Y-m-d H:i:s');
            $answerModel->isLastMessage = 1;

            if ($answerModel->validate()) {

                if (ContactNotify::sendNewAdminReply($answerModel)) {

                    $answerModel->save(false);

                    if (empty($model->code)) {
                        $model->generateCode();
                        $model->save(false);
                    }

                    \Yii::$app->getSession()->setFlash('success', Yii::t('contact', 'Your message has been sent.'));

                    return $this->redirect(['index']);
                } else {
                    \Yii::$app->getSession()->setFlash('error', Yii::t('contact', 'There was an error during sending email. Email not sent.'));
                }
            }
        }

        return $this->render('view', [
            'model' => $model,
            'answerModel' => $answerModel,
            'templateModel' => $templateModel,
            'templates' => ContactTemplate::find()->multilingual()->all()
        ]);
    }

    /**
     * Toggles close/open contact thread
     *
     * @param integer $id the ID of the model to be deleted
     * @return Response
     */
    public function actionToggleClose($id)
    {
        if (Yii::$app->request->isPost) {
            $model = $this->findModel($id);
            $model->isClosed = !$model->isClosed;
            $model->save(false);

            return $this->redirect(['index']);
        }
    }

    /**
     * Get contact thread preview
     *
     * @param integer $id the ID of the model to be previewed
     * @return string
     */
    public function actionPreview($id)
    {
        $model = $this->findModel($id);

        return $this->renderAjax('_contact-preview', [
            'model' => $model,
        ]);
    }

    public function actionCreateTemplate()
    {
        $model = new ContactTemplate();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->refresh();

            Yii::$app->response->format = 'json';

            return [
                'message' => Yii::t('contact', 'New template has been successfully created.')
            ];
        }

        return $this->renderAjax('_template-form', [
            'model' => $model,
        ]);
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

    /**
     * Finds the Contact model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Contact the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contact::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}