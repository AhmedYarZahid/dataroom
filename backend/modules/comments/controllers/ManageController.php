<?php

namespace app\modules\comments\controllers;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use backend\modules\comments\models\CommentBundle;
use backend\modules\comments\models\CommentBundleSearch;
use backend\modules\comments\models\Comment;
use backend\modules\comments\models\CommentSearch;

class ManageController extends \backend\controllers\Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'Comments');
        $this->titleSmall = Yii::t('admin', 'Manage comments');

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
     * Lists all nodes with comments
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CommentBundleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('bundles', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates a particular node
     *
     * @author Petr Dvukhrechensky <petr.sdkb@gmail.com>
     *
     * @param integer $id the ID of comment bundle
     * @return Response
     */
    public function actionUpdateBundle($id)
    {
        $model = $this->findBundleModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save(false)) {
                    \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Comments settings have been updated successfully.'));

                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('update-bundle', [
            'model' => $model,
        ]);
    }

    /**
     * Lists comments for a node
     *
     * @param integer $id CommentBundleID
     * @return string
     */
    public function actionComments($id)
    {
        $commentBundle = $this->findBundleModel($id);

        $searchModel = new CommentSearch();
        $searchModel->commentBundleID = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('comments', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates a comment
     *
     * @author Petr Dvukhrechensky <petr.sdkb@gmail.com>
     *
     * @param integer $id the ID of the comment to be updated
     * @return Response
     */
    public function actionUpdateComment($id)
    {
        $model = $this->findCommentModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save(false)) {
                    \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Comment has been updated successfully.'));

                    return $this->redirect(['comments', 'id' => $model->commentBundleID]);
                }
            }
        }

        return $this->render('update-comment', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes comment
     *
     * @param integer $id the ID of the model to be deleted
     * @return Response
     */
    public function actionDeleteComment($id)
    {
        $this->findCommentModel($id)->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Toggles isActive property of CommentBundle model.
     * @param integer $id
     * @return mixed
     */
    public function actionToggleBundleActivity($id)
    {
        $response = [
            'status' => 'success',
            'errors' => [],
        ];

        $model = $this->findBundleModel($id);
        $model->isActive = 1 - $model->isActive;

        if (!$model->save(false)) {
            $response['status'] = 'error';
            $response['errors'][] = 'Database error';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;
    }

    /**
     * Toggles isNewCommentsAllowed property of CommentBundle model.
     * @param integer $id
     * @return mixed
     */
    public function actionToggleBundleIsNewCommentsAllowed($id)
    {
        $response = [
            'status' => 'success',
            'errors' => [],
        ];

        $model = $this->findBundleModel($id);
        $model->isNewCommentsAllowed = 1 - $model->isNewCommentsAllowed;

        if (!$model->save(false)) {
            $response['status'] = 'error';
            $response['errors'][] = 'Database error';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;
    }

    /**
     * Toggles isApproved property of Comment model.
     * @param integer $id
     * @return mixed
     */
    public function actionToggleCommentApproval($id)
    {
        $response = [
            'status' => 'success',
            'errors' => [],
        ];

        $model = $this->findCommentModel($id);
        $model->isApproved = 1 - $model->isApproved;

        if (!$model->save(false)) {
            $response['status'] = 'error';
            $response['errors'][] = 'Database error';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;
    }

    /**
     * Finds the CommentBundle model by its ID
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return CommentBundle the loaded model
     * @throws NotFoundHttpException
     */
    protected function findBundleModel($id)
    {
        $query = CommentBundle::find();
        $query->where(['id' => $id]);

        if (($model = $query->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested node does not exist.');
    }

    /**
     * Finds the Comment model by its ID
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Comment the loaded model
     * @throws NotFoundHttpException
     */
    protected function findCommentModel($id)
    {
        $query = Comment::find();
        $query->where(['id' => $id]);

        if (($model = $query->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested comment does not exist.');
    }
}
