<?php

namespace backend\modules\dataroom\controllers;

use backend\modules\dataroom\models\RoomAccessRequest;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use backend\modules\dataroom\models\AbstractAccessRequest;
use backend\modules\notify\models\Notify;

abstract class AbstractAccessRequestController extends \backend\controllers\Controller
{
    public $title = 'AJA Dataroom';

    public $roomType;
    public $controllerID;

    protected $modelClass;
    protected $searchModelClass;

    /**
     * @inheritdoc
     */
    public function __construct($id, $module, $config = [])
    {
        list($this->roomType, $this->controllerID) = explode('/', $id);

        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
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
     * Lists all rooms.
     */
    public function actionIndex()
    {
        $this->layout = 'manage-rooms';

        $searchModel = new $this->searchModelClass;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
        ]);
    }

    /**
     * Validates an access request.
     */
    public function actionValidate($id)
    {
        $model = $this->findModel($id);
        $accessRequest = $model->accessRequest;

        if ($accessRequest->status == RoomAccessRequest::STATUS_WAITING) {
            $accessRequest->validatedBy = Yii::$app->user->id;
            $accessRequest->status = RoomAccessRequest::STATUS_ACCEPTED;
            $accessRequest->save();

            Notify::sendAccessRequestValidated($model);

            Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Request has been validated.'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Refuses an access request.
     */
    public function actionRefuse($id)
    {
        $model = $this->findModel($id);
        $accessRequest = $model->accessRequest;

        if ($accessRequest->status == RoomAccessRequest::STATUS_WAITING) {
            $accessRequest->refusedBy = Yii::$app->user->id;
            $accessRequest->status = RoomAccessRequest::STATUS_REFUSED;
            $accessRequest->save();

            Notify::sendAccessRequestRefused($model);

            Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Request has been refused.'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the AbstractAccessRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AbstractAccessRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $modelClass = $this->modelClass;

        if (($model = $modelClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
