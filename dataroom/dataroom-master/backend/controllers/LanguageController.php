<?php

namespace backend\controllers;

use Yii;
use common\models\Language;
use common\models\LanguageSearch;
use backend\controllers\Controller;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LanguageController implements the CRUD actions for Language model.
 */
class LanguageController extends Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'Languages');
        $this->titleSmall = Yii::t('admin', 'Manage languages');

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
     * Lists all Language models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LanguageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Language model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Language model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Language();

        if ($model->load(Yii::$app->request->post())) {

            $ts = Yii::$app->db->beginTransaction();

            try {
                if ($model->save()) {

                    // --- Add new language for necessary tables --- //

                    $condition = "languageID = '" . Yii::$app->params['defaultLanguageID'] . "'";

                    // Menu
                    Yii::$app->db->createCommand("
                        INSERT INTO MenuLang (menuID, languageID, title)
                        SELECT menuID, '" . $model->id . "', title
                        FROM MenuLang WHERE " . $condition . "
                    ")->execute();

                    // Contact Template
                    Yii::$app->db->createCommand("
                        INSERT INTO ContactTemplateLang (contactTemplateID, languageID, name, body)
                        SELECT contactTemplateID, '" . $model->id . "', name, body
                        FROM ContactTemplateLang WHERE " . $condition . "
                    ")->execute();

                    // FAQ Item
                    Yii::$app->db->createCommand("
                        INSERT INTO FaqItemLang (faqItemID, languageID, question, answer)
                        SELECT faqItemID, '" . $model->id . "', question, answer
                        FROM FaqItemLang WHERE " . $condition . "
                    ")->execute();

                    // FAQ Category
                    Yii::$app->db->createCommand("
                        INSERT INTO FaqCategoryLang (faqCategoryID, languageID, title)
                        SELECT faqCategoryID, '" . $model->id . "', title
                        FROM FaqCategoryLang WHERE " . $condition . "
                    ")->execute();

                    // News
                    Yii::$app->db->createCommand("
                        INSERT INTO NewsLang (newsID, languageID, title, body, slug)
                        SELECT newsID, '" . $model->id . "', title, body, slug
                        FROM NewsLang WHERE " . $condition . "
                    ")->execute();

                    // Notify
                    Yii::$app->db->createCommand("
                        INSERT INTO NotifyLang (notifyID, languageID, title, subject, body)
                        SELECT notifyID, '" . $model->id . "', title, subject, body
                        FROM NotifyLang WHERE " . $condition . "
                    ")->execute();

                    // Trendy Pages
                    Yii::$app->db->createCommand("
                        INSERT INTO TrendyPageLang (trendyPageID, languageID, title, body, bodyData, slug)
                        SELECT trendyPageID, '" . $model->id . "', title, body, bodyData, slug
                        FROM TrendyPageLang WHERE " . $condition . "
                    ")->execute();

                    // Trendy Page Revisions
                    Yii::$app->db->createCommand("
                        INSERT INTO TrendyPageHistoryLang (trendyPageHistoryID, languageID, title, body, bodyData, slug)
                        SELECT trendyPageHistoryID, '" . $model->id . "', title, body, bodyData, slug
                        FROM TrendyPageHistoryLang WHERE " . $condition . "
                    ")->execute();

                    // Form Pages
                    Yii::$app->db->createCommand("
                        INSERT INTO FormPageLang (formPageID, languageID, title, body, bodyData, slug)
                        SELECT formPageID, '" . $model->id . "', title, body, bodyData, slug
                        FROM FormPageLang WHERE " . $condition . "
                    ")->execute();

                    // Form Page Revisions
                    Yii::$app->db->createCommand("
                        INSERT INTO FormPageHistoryLang (formPageHistoryID, languageID, title, body, bodyData, slug)
                        SELECT formPageHistoryID, '" . $model->id . "', title, body, bodyData, slug
                        FROM FormPageHistoryLang WHERE " . $condition . "
                    ")->execute();

                    $ts->commit();

                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $ts->rollBack();

            } catch (Exception $e) {
                $ts->rollBack();
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Language model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
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
     * Deletes an existing language
     *
     * @param string $id
     *
     * @return mixed
     *
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!$model->isAllowDelete()) {
            throw new BadRequestHttpException("Bad request");
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Language model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Language the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Language::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
