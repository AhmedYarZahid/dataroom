<?php

namespace backend\controllers;

use arturoliveira\ExcelView;
use common\helpers\ArrayHelper;
use ruskid\csvimporter\CSVImporter;
use ruskid\csvimporter\CSVReader;
use ruskid\csvimporter\MultipleImportStrategy;
use Yii;
use common\models\Newsletter;
use common\models\NewsletterSearch;
use backend\controllers\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * NewsletterController implements the CRUD actions for Newsletter model.
 */
class NewsletterController extends Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'Newsletter Subscribers');
        $this->titleSmall = Yii::t('admin', 'Manage newsletter subscribers');

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
     * Lists all Newsletter models.
     * @return mixed
     */
    public function actionIndex()
    {
        if ($emailsListFile = UploadedFile::getInstanceByName('emailsList')) {

            $importer = new CSVImporter();

            //Will read CSV file
            $importer->setData(new CSVReader([
                'filename' => $emailsListFile->tempName,
                'fgetcsvOptions' => [
                    'delimiter' => ';'
                ],
                'startFromLine' => 0
            ]));

            $totalRows = count($importer->getData());

            $numberRowsAffected = $importer->import(new MultipleImportStrategy([
                'tableName' => Newsletter::tableName(),
                'configs' => [
                    [
                        'attribute' => 'email',
                        'value' => function($line) {
                            $email = trim($line[0]);

                            return $email;
                        },
                    ],
                    [
                        'attribute' => 'firstName',
                        'value' => function($line) {
                            $value = trim($line[1]);
                            return $value;
                        },
                    ],
                    [
                        'attribute' => 'lastName',
                        'value' => function($line) {
                            $value = trim($line[2]);
                            return $value;
                        },
                    ],
                ],
                'skipImport' => function($line) {
                    $email = trim($line[0]);

                    return Newsletter::find()->where(['email' => $email])->exists();
                }
            ]));

            Yii::$app->session->setFlash('success', Yii::t('admin', '{rows-imported} email(s) has been imported (out of {rows-total}).', ['rows-imported' => $numberRowsAffected, 'rows-total' => $totalRows]));

            return $this->refresh();
        }

        $searchModel = new NewsletterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Newsletter model.
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
     * Creates a new Newsletter model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Newsletter();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Newsletter model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Newsletter model.
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
     * Finds the Newsletter model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Newsletter the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Newsletter::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
