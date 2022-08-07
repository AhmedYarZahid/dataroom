<?php

namespace backend\modules\dataroom\controllers;

use backend\modules\dataroom\models\AbstractProposal;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use backend\modules\dataroom\ProposalManager;
use backend\modules\dataroom\exceptions\ManagerNotCreatedException;
use backend\modules\dataroom\models\AbstractDetailedRoom;
use backend\modules\dataroom\models\Room;
use backend\modules\dataroom\models\Proposal;
use backend\modules\notify\models\Notify;

abstract class AbstractProposalController extends \backend\controllers\Controller
{
    public $title = 'AJA Dataroom';

    public $roomType;
    public $controllerID;

    protected $modelClass;
    protected $searchModelClass;

    protected $proposalFileName;

    protected $proposalManager;

    public function __construct($id, 
                                $controller,
                                ProposalManager $proposalManager, 
                                $config = [])
    {
        $this->proposalManager = $proposalManager;

        list($this->roomType, $this->controllerID) = explode('/', $id);

        parent::__construct($id, $controller, $config);
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

    public function actionCreate($roomId)
    {
        $room = Room::findOne($roomId);
        if (!$room) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $proposal = new $this->modelClass;
        $user = Yii::$app->user->identity;

        try {
            $created = $proposal->load(Yii::$app->request->post())
                && $this->proposalManager->createByAdmin($room, $proposal, $user);

            if ($created) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Proposal was created successfully.'));

                Notify::sendNewProposalToBuyer($proposal);
                Notify::sendNewProposalToAdmin($proposal);

                return $this->redirect(['index']);
            }
        } catch (Exception $e) {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', "There was an error creating proposal. Please try again."));
        }

        return $this->render('create', [
            'proposal' => $proposal,
            'room' => $room,
        ]);
    }

    public function actionDownloadTemplate()
    {
        $proposal = new $this->modelClass;

        $path = $proposal->templatePath();

        if ($path) {
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            return Yii::$app->response->sendFile($path, $this->proposalFileName . '.' . $ext);
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the AbstractProposal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AbstractProposal the loaded model
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
