<?php

namespace backend\modules\contact\controllers;

use backend\modules\contact\models\ContactTemplate;
use backend\modules\contact\models\ContactTemplateSearch;
use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use common\helpers\FileHelper;
use yii\filters\AccessControl;


class ManageTemplateController extends \backend\controllers\Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'Contact Templates');
        $this->titleSmall = Yii::t('admin', 'Manage contact templates');

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
     * Templates list
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ContactTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new template
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ContactTemplate();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post())) {

            $contactTemplateModel = new ContactTemplate();
            $contactTemplateModel->save(false);

            foreach (Yii::$app->params['languagesList'] as $languageModel) {
                Yii::$app->db->createCommand("
                    INSERT INTO ContactTemplateLang (contactTemplateID, languageID, name, body)
                    VALUES (:contactTemplateID, :languageID, :name, :body)
                ", [
                    'contactTemplateID' => $contactTemplateModel->id,
                    'languageID' => $languageModel->id,
                    'name' => $model->name,
                    'body' => $model->body,
                ])->execute();
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'id' => $contactTemplateModel->id,
                    'name' => $model->name,
                    'body' => $model->body,
                ];
            } else {
                \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'New template has been created successfully.'));

                return $this->redirect(['index', 'id' => $model->id]);
            }

        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing template
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id, true);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Template has been updated successfully.'));

            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes a particular template
     *
     * @param integer $id the ID of the model to be deleted
     * @return Response
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ContactTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param bool $multilingual
     * @return ContactTemplate the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id, $multilingual = false)
    {
        $query = ContactTemplate::find();
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