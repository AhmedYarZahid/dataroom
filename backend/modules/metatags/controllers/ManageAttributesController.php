<?php

namespace app\modules\metatags\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use backend\modules\metatags\models\MetaTagsAttrs;
use yii\filters\AccessControl;


class ManageAttributesController extends \backend\controllers\Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'Meta tags attributes values');
        $this->titleSmall = Yii::t('admin', 'Manage meta tags attributes values');

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
     * List of meta tags attributes values
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = MetaTagsAttrs::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a meta tag attributes value
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MetaTagsAttrs();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            \Yii::$app->getSession()->setFlash('success',
                Yii::t('metatags', 'New meta tag attribute value has been created successfully.'));

            return $this->redirect(['index', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Meta tags attributes value
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Meta tags attribute value has been updated successfully.'));

            return $this->redirect(['index', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing meta tag attribute value
     *
     * @param string $id
     *
     * @return mixed
     *
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MetaTagsAttrs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MetaTagsAttrs the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MetaTagsAttrs::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested record does not exist.');
        }
    }
}