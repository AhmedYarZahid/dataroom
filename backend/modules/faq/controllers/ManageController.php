<?php

namespace backend\modules\faq\controllers;

use backend\modules\faq\models\FaqCategory;
use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use common\helpers\FileHelper;
use backend\modules\faq\models\FaqItem;
use backend\modules\faq\models\FaqItemSearch;
use yii\filters\AccessControl;


class ManageController extends \backend\controllers\Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'FAQ');
        $this->titleSmall = Yii::t('admin', 'Manage FAQ');

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
     * @param $faqCategoryID
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($faqCategoryID)
    {
        $params = array_merge([], Yii::$app->request->queryParams);
        if (!isset($params['FaqItemSearch']) || !is_array($params['FaqItemSearch'])) {
            $params['FaqItemSearch'] = [];
        }
        $params['FaqItemSearch']['faqCategoryID'] = $faqCategoryID;

        $category = $this->findCategory($faqCategoryID);
        $searchModel = new FaqItemSearch();
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'category' => $category,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $faqCategoryID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionCreate($faqCategoryID)
    {
        $category = $this->findCategory($faqCategoryID);
        $model = new FaqItem();
        $model->scenario = 'create';

        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $ts = Yii::$app->db->beginTransaction();

            try {
                $model->save(false);

                foreach (Yii::$app->params['languagesList'] as $languageModel) {
                    if ($languageModel->id != Yii::$app->params['defaultLanguageID']) {
                        Yii::$app->db->createCommand("
                        INSERT INTO FaqItemLang (FaqItemID, languageID, question, answer)
                        SELECT " . $model->id . ", '" . $languageModel->id . "', question, answer
                        FROM FaqItemLang WHERE FaqItemID = " . $model->id . " AND languageID = '" . Yii::$app->params['defaultLanguageID'] . "'
                    ")->execute();
                    }
                }

                $ts->commit();

                \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'FAQ record created successfully'));

                return $this->redirect(['index', 'faqCategoryID' => $category->id]);

            } catch (Exception $e) {
                $ts->rollBack();
            }
        }

        $model->faqCategoryID = $faqCategoryID;

        return $this->render('create', [
            'model' => $model,
            'category' => $category,
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
        $category = $this->findCategory($model->faqCategoryID);

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Category updated successfully.'));

                return $this->redirect(['index', 'faqCategoryID' => $category->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'lang' => $lang,
            'category' => $category,
        ]);
    }

    /**
     * Deletes an existing FaqItem model
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $categoryID = $model->faqCategoryID;

        $model->delete();

        return $this->redirect(['index', 'faqCategoryID' => $categoryID]);
    }

    /**
     * Finds the FaqItem model based on its primary key value
     *
     * @param integer $id
     * @param bool $multilingual
     * @return FaqItem the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id, $multilingual = false)
    {
        $query = FaqItem::find();
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
     * Find FaqCategory by ID
     *
     * @param integer $id
     * @return FaqCategory
     * @throws NotFoundHttpException
     */
    protected function findCategory($id)
    {
        $category = FaqCategory::findOne($id);
        if ($category === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $category;
    }

    /**
     * Upload image using Imperavi redactor
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function actionUploadImage()
    {
        $directory = Yii::getAlias('@uploads/faq/');
        $file = md5(date('YmdHis')) . '.' . pathinfo(@$_FILES['file']['name'], PATHINFO_EXTENSION);

        $array = [];
        if (move_uploaded_file(@$_FILES['file']['tmp_name'], $directory . $file)) {
            $array = ['url' => Yii::getAlias('@uploads/faq-rel/') . $file];
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
        $imagesList = FileHelper::findFiles(Yii::getAlias('@uploads/faq'));

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