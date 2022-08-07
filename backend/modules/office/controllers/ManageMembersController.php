<?php

namespace backend\modules\office\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use backend\modules\office\models\OfficeMember;
use backend\modules\office\models\OfficeMemberSearch;

class ManageMembersController extends \backend\controllers\Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'Members');
        $this->titleSmall = Yii::t('admin', 'Manage members');

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

    public function actionIndex()
    {
        $searchModel = new OfficeMemberSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new OfficeMember;
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->saveUploadedImage();

            if ($model->save(false)) {
                Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Member has been created successfully.'));

                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldImage = $model->image;
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $imageUploaded = $model->saveUploadedImage();

            if ($model->save(false)) {
                if ($imageUploaded) {
                    $model->setOldAttribute('image', $oldImage);
                    $model->removeOldImage();
                }

                Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Member has been updated successfully.'));

                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete()) {
            $model->setOldAttribute('image', $model->image);
            $model->removeOldImage();
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        $query = OfficeMember::find();
        $query->where(['id' => $id]);

        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}