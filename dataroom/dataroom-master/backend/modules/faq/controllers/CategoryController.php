<?php

namespace backend\modules\faq\controllers;

use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use backend\modules\faq\models\FaqCategory;
use backend\modules\faq\models\FaqCategorySearch;
use yii\filters\AccessControl;


class CategoryController extends \backend\controllers\Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'FAQ Categories');
        $this->titleSmall = Yii::t('admin', 'Manage FAQ categories');

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
     * FAQ categories list
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FaqCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Create new category
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FaqCategory();
        $model->scenario = 'create';

        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $ts = Yii::$app->db->beginTransaction();

            try {
                $model->save(false);

                foreach (Yii::$app->params['languagesList'] as $languageModel) {
                    if ($languageModel->id != Yii::$app->params['defaultLanguageID']) {
                        Yii::$app->db->createCommand("
                        INSERT INTO FaqCategoryLang (faqCategoryID, languageID, title)
                        SELECT " . $model->id . ", '" . $languageModel->id . "', title
                        FROM FaqCategoryLang WHERE faqCategoryID = " . $model->id . " AND languageID = '" . Yii::$app->params['defaultLanguageID'] . "'
                    ")->execute();
                    }
                }

                $ts->commit();

                \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Category created successfully'));

                return $this->redirect(['index', 'id' => $model->id]);

            } catch (Exception $e) {
                $ts->rollBack();
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Update FAQ category
     *
     * @param integer $id
     * @param string $lang
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id, $lang = null)
    {
        if (!$lang) {
            $lang = Yii::$app->params['defaultLanguageID'];
        } else {
            Yii::$app->params['defaultLanguageID'] = $lang;
        }

        $model = $this->findModel($id, $lang);
        $model->scenario = 'update';

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Category updated successfully.'));

                return $this->redirect(['index', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'lang' => $lang,
        ]);
    }

    /**
     * Deletes an existing FaqCategory model
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the FaqCategory model based on its primary key value
     *
     * @param integer $id
     * @param bool $multilingual
     * @return FaqCategory the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id, $multilingual = false)
    {
        $query = FaqCategory::find();
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