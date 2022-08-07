<?php

namespace backend\modules\mailing\controllers;

use backend\modules\dataroom\models\Room;
use backend\modules\dataroom\models\RoomAccessRequest;
use common\actions\FindRoomsAction;
use common\models\User;
use Exception;
use Yii;
use yii\db\ActiveQuery;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\helpers\FileHelper;
use backend\modules\mailing\managers\CampaignManager;
use backend\modules\mailing\models\MailingCampaign;
use backend\modules\mailing\models\MailingCampaignSearch;

class CampaignController extends \backend\controllers\Controller
{
    public $title = 'AJA List';
    public $titleSmall = 'Gérer les listes de diffusion';
    public $layout = 'main';

    protected $manager;

    public function __construct($id, $controller, CampaignManager $manager, $config = [])
    {
        $this->manager = $manager;

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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'find-rooms' => [
                'class' => FindRoomsAction::class
            ],
        ];
    }

    /**
     * Displays all lists.
     */
    public function actionIndex()
    {
        $searchModel = new MailingCampaignSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $stats = [];
        try {
            $stats = Yii::$app->mailjet->getCampaignStats();
        } catch (Exception $e) {
            Yii::error($e->__toString(), 'mailing');
            Yii::$app->session->setFlash('warning', Yii::t('admin', "We couldn't load data from Mailjet. Please try to reload the page to get info about sent/unsent/opened emails."));
        }
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'stats' => $stats,
        ]);
    }

    /**
     * Creates a new campaign.
     */
    public function actionCreate($listID = null)
    {
        $model = new MailingCampaign;
        $model->userID = Yii::$app->user->id;
        $model->listID = $listID;
        $model->sender = Yii::$app->mailjet->sender;

        $randomAdmin = User::find()->active()->ofType(User::TYPE_ADMIN)->one();
        $model->testTo = $randomAdmin ? $randomAdmin->email : Yii::$app->params['adminEmail'];

        $input = Yii::$app->request->post();

        if ($model->load($input)) {
            $model->scenario = $input['scenario'];

            if ($this->manager->save($model)) {
                switch ($model->scenario) {
                    case $model::SCENARIO_CREATE_OR_UPDATE:
                        $message = Yii::t('admin', 'New campaign has been created successfully.');
                        break;
                    
                    case $model::SCENARIO_TEST_EMAIL:
                        $message = Yii::t('admin', 'Test email has been sent.');
                        break;

                    case $model::SCENARIO_SEND:
                        $message = Yii::t('admin', 'Campaign has been sent.');
                        break;
                }
                Yii::$app->session->setFlash('success', $message);

                return $this->redirect(['update', 'id' => $model->id]);
            } else if ($model->scenario != $model::SCENARIO_CREATE_OR_UPDATE) {
                $errors = $this->manager->getEmailErrors();
                if ($errors) {
                    Yii::$app->session->setFlash('error', implode("\n", $errors));    
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Send email to multiple users of the specified room
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $roomID
     * @return string|Response
     * @throws BadRequestHttpException
     */
    public function actionSendEmailToRoomUsers($roomID)
    {
        $this->layout = null;

        $model = new MailingCampaign;
        $model->userID = Yii::$app->user->id;
        $model->sender = Yii::$app->mailjet->sender;
        //$model->sender = 'forlgc@gmail.com';

        $randomAdmin = User::find()->active()->ofType(User::TYPE_ADMIN)->one();
        $model->testTo = $randomAdmin ? $randomAdmin->email : Yii::$app->params['adminEmail'];

        $model->scenario = MailingCampaign::SCENARIO_EMAIL_TO_ROOM_USERS;

        if (!$roomModel = Room::findOne($roomID)) {
            throw new BadRequestHttpException('Invalid request. Room not exists.');
        }

        $recipientIDs = Room::find()
            ->select('RoomAccessRequest.userID')
            ->where(['Room.id' => $roomID])
            ->innerJoinWith(['roomAccessRequests' => function (ActiveQuery $query) {
                $query->andOnCondition(['IS NOT', 'validatedBy', null]);
                $query->innerJoinWith(['user' => function (ActiveQuery $query) {
                    $query->andOnCondition(['isActive' => 1]);
                }]);
            }])
            ->column();
        $model->recipientIDs = $recipientIDs;

        $input = Yii::$app->request->post();

        if ($model->load($input)) {
            $model->scenario = $input['scenario'];

            if ($model->validate()) {
                switch ($model->scenario) {
                    case $model::SCENARIO_TEST_EMAIL:
                        $result = $this->manager->sendTestEmail($model, false);

                        break;

                    case $model::SCENARIO_EMAIL_TO_ROOM_USERS:
                        $result = $this->manager->sendToUsers($model);

                        break;

                    default:
                        throw new BadRequestHttpException('Invalid request.');
                }

                if ($result) {
                    switch ($model->scenario) {
                        case $model::SCENARIO_TEST_EMAIL:
                            $message = Yii::t('admin', 'Test email has been sent.');
                            break;

                        case $model::SCENARIO_EMAIL_TO_ROOM_USERS:
                            $message = Yii::t('admin', 'Email(s) has been sent.');
                            break;

                        default:
                            throw new BadRequestHttpException('Invalid request.');
                    }

                    Yii::$app->session->setFlash('success', $message);

                    return $this->redirect($model->scenario == $model::SCENARIO_EMAIL_TO_ROOM_USERS ? ['/dataroom'] : ['send-email-to-room-users', 'roomID' => $roomID]);

                } else {
                    $errors = $this->manager->getEmailErrors();
                    if ($errors) {
                        Yii::$app->session->setFlash('error', implode("\n", $errors));
                    }
                }
            }
        }

        return $this->render('send-email-to-room-users', [
            'model' => $model,
            'roomModel' => $roomModel,
            'recipientIDs' => $recipientIDs,
        ]);
    }

    /**
     * Updates a campaign.
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $randomAdmin = User::find()->active()->ofType(User::TYPE_ADMIN)->one();
        $model->testTo = $randomAdmin ? $randomAdmin->email : Yii::$app->params['adminEmail'];

        if ($model->status == $model::STATUS_SENT) {
            return $this->redirect(['index']);
        }

        $input = Yii::$app->request->post();

        if ($model->load($input)) {
            $model->scenario = $input['scenario'];

            if ($this->manager->save($model)) {
                switch ($model->scenario) {
                    case $model::SCENARIO_CREATE_OR_UPDATE:
                        $message = Yii::t('admin', 'Campaign has been updated successfully.');
                        break;
                    
                    case $model::SCENARIO_TEST_EMAIL:
                        $message = Yii::t('admin', 'Test email has been sent.');
                        break;

                    case $model::SCENARIO_SEND:
                        $message = Yii::t('admin', 'Campaign has been sent.');
                        break;
                }
                Yii::$app->session->setFlash('success', $message);

                return $this->refresh();
            } else if ($model->scenario != $model::SCENARIO_CREATE_OR_UPDATE) {
                $errors = $this->manager->getEmailErrors();
                if ($errors) {
                    Yii::$app->session->setFlash('error', implode("\n", $errors));    
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes a list.
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Upload image using Imperavi redactor
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function actionUploadImage()
    {
        $directory = Yii::getAlias('@uploads/editor/');
        $file = md5(date('YmdHis')) . '.' . pathinfo(@$_FILES['file']['name'], PATHINFO_EXTENSION);

        $array = [];
        if (move_uploaded_file(@$_FILES['file']['tmp_name'], $directory . $file)) {
            $array = ['url' => Yii::getAlias('@uploads/editor-rel/') . $file];
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
        $imagesList = FileHelper::findFiles(Yii::getAlias('@uploads/editor'));

        $result = array();
        foreach ($imagesList as $image) {
            $result[] = array(
                'thumb' => str_replace(Yii::getAlias('@uploads-webroot'), '', $image),
                'url' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(str_replace(Yii::getAlias('@uploads-webroot'), '', $image)),
                //'title' => 'Title1', // optional
                //'folder' => 'myFolder' // optional
            );
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $result;
    }

    /**
     * Finds the MailingCampaign model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MailingCampaign the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MailingCampaign::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
