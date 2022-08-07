<?php

namespace frontend\modules\dataroom\controllers;

use backend\modules\dataroom\models\RoomCompany;
use backend\modules\dataroom\models\RoomCoownership;
use backend\modules\dataroom\models\RoomCV;
use backend\modules\dataroom\models\RoomRealEstate;
use backend\modules\dataroom\UserManager;
use backend\modules\notify\models\Notify;
use backend\modules\document\models\Document;
use backend\modules\document\models\DocumentSearch;
use common\components\DocumentBehavior;
use common\components\managers\DocumentManager;
use common\helpers\ArrayHelper;
use Exception;
use kartik\form\ActiveForm;
use Yii;
use yii\base\NotSupportedException;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use common\models\User;
use backend\modules\dataroom\RoomManager;
use backend\modules\dataroom\AccessRequestManager;
use backend\modules\dataroom\ProposalManager;
use backend\modules\dataroom\models\Room;
use backend\modules\dataroom\models\AbstractDetailedRoom;
use yii\web\Response;
use backend\modules\dataroom\Module as DataroomModule;

abstract class AbstractRoomController extends AbstractController
{
    protected $modelClass;
    protected $searchModelClass;
    protected $accessRequestClass;
    protected $proposalClass;

    protected $proposalFileName;

    protected $roomManager;
    protected $accessRequestManager;
    protected $proposalManager;
    protected $documentManager;
    protected $userManager;

    /**
     * @var AbstractDetailedRoom current detailed room model for which we view data now (needed for navigation between room tabs)
     */
    public $detailedRoomModel;

    public function __construct($id,
                                $controller,
                                RoomManager $roomManager,
                                AccessRequestManager $accessRequestManager,
                                ProposalManager $proposalManager,
                                DocumentManager $documentManager,
                                UserManager $userManager,
                                $config = [])
    {
        $this->roomManager = $roomManager;
        $this->accessRequestManager = $accessRequestManager;
        $this->proposalManager = $proposalManager;
        $this->documentManager = $documentManager;
        $this->userManager = $userManager;

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
                'only' => [
                    'view-room', 'update-room', 'get-access', 'proposal', 'documents', 'create-document', 'update-document',
                    'delete-document',
                    'create-multiple-documents', 'update-document-title', 'create-document-folder', 'set-documents-order',
                    'manage-documents-tree', 'download-all-documents', 'download-document'
                ],
                'rules' => [
                    [
                        'actions' => ['view-room'],
                        'allow' => true,
                        'roles' => ['viewRoom'],
                        'roleParams' => function() {
                            return ['room' => $this->findRoomModel(Yii::$app->request->get('id'))];
                        },
                    ],
                    [
                        'actions' => ['documents', 'manage-documents-tree', 'download-all-documents', 'download-document'],
                        'allow' => true,
                        'roles' => ['seeRoomDetails'],
                        'roleParams' => function() {
                            if ($this->action->id == 'download-document') {
                                $room = Document::findOne(Yii::$app->request->get('id'))->room;
                            } else if ($this->action->id == 'download-all-documents') {
                                $room = $this->findRoomModel(Yii::$app->request->get('roomID'));
                            } else {
                                $room = $this->findRoomModel(Yii::$app->request->get('id'));
                            }
                            return ['room' => $room];
                        },
                    ],
                    [
                        //'actions' => ['update-room', 'create-document'],
                        'actions' => ['update-room'],
                        'allow' => true,
                        'roles' => ['updateRoom'],
                        'roleParams' => function() {
                            return ['room' => $this->findRoomModel(Yii::$app->request->get('id'))];
                        },
                    ],
                    /*[
                        'actions' => ['update-document', 'delete-document'],
                        'allow' => true,
                        'roles' => ['updateRoom'],
                        'roleParams' => function() {
                            return ['room' => Document::findOne(Yii::$app->request->get('documentID'))->room];
                        },
                    ],*/
                    [
                        'actions' => ['get-access'],
                        'allow' => true,
                        'roles' => ['askForAccess'],
                        'roleParams' => function() {
                            return ['room' => $this->findRoomModel(Yii::$app->request->get('id'))];
                        },
                    ],
                    [
                        'actions' => ['proposal', 'download-proposal-file'],
                        'allow' => true,
                        'roles' => ['makeProposal'],
                        'roleParams' => function() {
                            return ['room' => $this->findRoomModel(Yii::$app->request->get('id'))];
                        },
                    ],

                    // TODO: sort out rights about documents
                    [
                        'actions' => ['create-document', 'create-multiple-documents'],
                        'allow' => true,
                        'roles' => ['manager', 'admin'],
                    ],
                    [
                        'actions' => ['update-document', 'delete-document', 'update-document-title', 'create-document-folder', 'set-documents-order'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if ($action->id == 'get-access') {
                        return $this->redirect(['view-room', 'id' => Yii::$app->request->get('id')]);
                    } else {
                        if (Yii::$app->user->getIsGuest()) {
                            return Yii::$app->user->loginRequired();
                        } else {
                            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                        }
                    }
                }
            ],
        ];
    }

    /**
     * List all company rooms
     */
    public function actionIndex()
    {
        $this->layout = 'rooms-list';

        $searchModel = new $this->searchModelClass;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->searchPublished(Yii::$app->request->get()),
        ]);
    }

    /**
     * View room details
     *
     * @param int $id Room id.
     * @return string|Response
     */
    public function actionViewRoom($id)
    {
        $this->layout = 'manage-room';

        $this->detailedRoomModel = $this->findModel($id);

        // We should allow access for any CV Room in case user has already at least one access to any other CV room
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->identity->getCVAccessRequest($this->detailedRoomModel->room);
        }

        // Show an update form for a manager who has access to update the room
        if (Yii::$app->user->can('updateRoom', ['room' => $this->detailedRoomModel->room])) {
            return $this->redirect(['update-room', 'id' => $id]);
            //return $this->actionUpdateRoom($id);
        }

        $prevRoom = $this->detailedRoomModel->getPreviousRoom();
        $nextRoom = $this->detailedRoomModel->getNextRoom();
        $room = $this->detailedRoomModel->room;
        $user = Yii::$app->user->isGuest ? new User : Yii::$app->user->identity;

        // Track info about visit
        $room->addHistoryRecord($user);

        // Show detailed info for a user who has access to the room
        if (Yii::$app->user->can('seeRoomDetails', ['room' => $room])) {
            return $this->render('update-room', [
                'model' => $this->detailedRoomModel,
                'room' => $room,
                'user' => $user,
                'disabledMode' => true,
            ]);
        }

        return $this->render('view-room', [
            'model' => $this->detailedRoomModel,
            'user' => $user,
            'prevRoom' => $prevRoom,
            'nextRoom' => $nextRoom,
        ]);
    }

    /**
     * Update room
     *
     * @param int $id Room id.
     * @return string|Response
     */
    public function actionUpdateRoom($id)
    {
        $this->layout = 'manage-room';

        $this->detailedRoomModel = $this->findModel($id);
        $detailedRoomClass = get_class($this->detailedRoomModel);
        $this->detailedRoomModel->scenario = $detailedRoomClass::SCENARIO_UPDATE_FRONT;

        $room = $this->detailedRoomModel->room;
        $room->scenario = $room::SCENARIO_UPDATE_FRONT;

        try {
            $data = Yii::$app->request->post();
            $room->load($data); // manager can't edit room data so it will return false

            $updated = $this->detailedRoomModel->load($data)
                && $this->roomManager->updateRoom($room, $this->detailedRoomModel);

            if ($updated) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Room has been updated successfully.'));

                return $this->redirect(['view-room', 'id' => $this->detailedRoomModel->id]);
            }
        } catch (Exception $e) {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', "There was an error updating a room. Please try again."));
        }

        return $this->render('update-room', [
            'model' => $this->detailedRoomModel,
            'room' => $room,
        ]);
    }

    public function actionProposal($id)
    {
        if (!$this->proposalClass) {
            throw new NotSupportedException();
        }

        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;
        $proposal = new $this->proposalClass;

        try {
            $created = $proposal->load(Yii::$app->request->post())
                && $this->proposalManager->createProposal($model->room, $proposal, $user);

            if ($created) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Thank you for your proposal!'));

                Notify::sendNewProposalToBuyer($proposal);
                Notify::sendNewProposalToAdmin($proposal);

                return $this->redirect(['view-room', 'id' => $model->id]);
            }
        } catch (Exception $e) {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', "There was an error creating proposal. Please try again."));
        }

        return $this->render('proposal', [
            'roomDetails' => $model,
            'proposal' => $proposal,
        ]);
    }

    public function actionGetAccess($id)
    {
        $model = $this->findModel($id);
        $accessRequest = new $this->accessRequestClass;
        $isGuest = Yii::$app->user->isGuest;

        $dataroomSection = null;
        if ($this->modelClass == RoomCompany::class) {
            $dataroomSection = DataroomModule::SECTION_COMPANIES;
        } elseif ($this->modelClass == RoomRealEstate::class) {
            $dataroomSection = DataroomModule::SECTION_REAL_ESTATE;
        } elseif ($this->modelClass == RoomCoownership::class) {
            $dataroomSection = DataroomModule::SECTION_COOWNERSHIP;
        } elseif ($this->modelClass == RoomCV::class) {
            $dataroomSection = DataroomModule::SECTION_CV;
        } else {
            throw new BadRequestHttpException();
        }

        $profile = $this->userManager->getProfile(!$isGuest ? Yii::$app->user->identity : null, $dataroomSection);

        if ($isGuest) {
            $user = new User;
            $user->scenario = 'register';
        } else {
            $user = Yii::$app->user->identity;

            // We should allow access for any CV Room in case user has already at least one access to any other CV room
            if (Yii::$app->user->identity->getCVAccessRequest($model->room)) {
                return $this->redirect(['view-room', 'id' => $id]);
            }
        }

        try {
            $created = false;

            if ($post = Yii::$app->request->post()) {
                $accessRequest->load($post);
                $user->load($post);
                $profile->load($post);

                /*if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;

                    $validationResut = ActiveForm::validate($accessRequest);
                    $validationResut = ArrayHelper::mergeArray($validationResut, ActiveForm::validate($user));

                    return $validationResut;
                }*/

                $created = $this->accessRequestManager->createAccessRequest($model, $accessRequest, $user, $profile);
            }

            if ($created) {
                if ($isGuest) {
                    $message = Yii::t('app', 'You have been registered successfully. We will approve your request soon.');
                    Yii::$app->user->login($user);

                    Notify::sendSignupNotify($user);
                } else {
                    $message = Yii::t('app', 'Thank you for your request. We will approve it soon.');
                }

                Notify::sendNewAccessRequest($accessRequest);

                Yii::$app->getSession()->setFlash('success', $message);

                return $this->redirect(['view-room', 'id' => $model->id]);
            }
        } catch (Exception $e) {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', "There was an error. Please try again."));
        }

        return $this->render('get-access', [
            'roomModel' => $model,
            'accessRequest' => $accessRequest,
            'user' => $user,
            'profile' => $profile
        ]);
    }

    public function actionDownloadProposalFile()
    {
        $proposal = new $this->proposalClass;

        $path = $proposal->templatePath();

        if ($path) {
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            return Yii::$app->response->sendFile($path, $this->proposalFileName . '.' . $ext);
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * List of documents related to room
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDocuments($id)
    {
        $this->layout = 'manage-room';
        $this->detailedRoomModel = $this->findModel($id);

        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->detailedRoomModel->roomID, true, Yii::$app->user->identity->isBuyer());

        return $this->render('documents', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Adds a new document to the room
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionCreateDocument($id)
    {
        $this->layout = 'manage-room';
        $this->detailedRoomModel = $this->findModel($id);

        // Create folders for documents
        Document::createRoomDocumentsFolders($this->detailedRoomModel->roomID);

        $model = new Document();
        $model->scenario = $this->detailedRoomModel->room->section == DataroomModule::SECTION_COMPANIES ? 'add-room-document' : 'add-room-document-no-folder';

        if ($model->load(Yii::$app->request->post())) {

            if ($this->documentManager->createRoomDocument($model, $this->detailedRoomModel->roomID)) {
                \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Document has been created successfully.'));

                return $this->redirect([$this->detailedRoomModel->room->section == DataroomModule::SECTION_COMPANIES
                    ? 'manage-documents-tree'
                    : 'documents',
                    'id' => $this->detailedRoomModel->id
                ]);
            }
        }

        return $this->render('create-document', [
            'model' => $model,
        ]);
    }

    /**
     * Adds a new documents
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function actionCreateMultipleDocuments($id)
    {
        $this->layout = 'manage-room';
        $this->detailedRoomModel = $this->findModel($id);

        // Create default folders for documents (if needed)
        Document::createRoomDocumentsFolders($this->detailedRoomModel->roomID);

        $model = new Document();
        $model->scenario = $this->detailedRoomModel->room->section == DataroomModule::SECTION_COMPANIES ? 'add-room-documents' : 'add-room-documents-no-folder';

        if ($model->load(Yii::$app->request->post())) {

            if ($this->documentManager->createMultipleDocuments($model, $this->detailedRoomModel->roomID)) {
                \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Documents has been created successfully.'));

                return $this->redirect([$this->detailedRoomModel->room->section == DataroomModule::SECTION_COMPANIES
                    ? 'manage-documents-tree'
                    : 'documents',
                    'id' => $this->detailedRoomModel->id
                ]);
            }
        }

        return $this->render('create-multiple-documents', [
            'model' => $model,
        ]);
    }

    /**
     * Updates a particular room document
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $documentID the ID of the document to be updated
     * @return Response
     */
    public function actionUpdateDocument($documentID)
    {
        $this->layout = 'manage-room';

        $model = Document::findOne($documentID);

        $model->scenario = $model->room->section == DataroomModule::SECTION_COMPANIES ? 'update-room-document' : 'update-room-document-no-folder';
        $this->detailedRoomModel = $this->findModel($model->room->detailedRoom->id); // TODO: makes no sense to have 'id' field as primary key for RoomCompany table

        $oldFilePath = $model->filePath;

        if ($model->load(Yii::$app->request->post())) {

            if ($this->documentManager->updateRoomDocument($model, $oldFilePath)) {
                \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Document has been updated successfully.'));

                return $this->redirect([$this->detailedRoomModel->room->section == DataroomModule::SECTION_COMPANIES
                    ? 'manage-documents-tree'
                    : 'documents',
                    'id' => $this->detailedRoomModel->id
                ]);
            }
        }

        return $this->render('update-document', [
            'model' => $model,
        ]);
    }

    /**
     * Updates a document title
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $documentID the ID of the document to be updated
     * @param string $title
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionUpdateDocumentTitle($documentID, $title)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->documentManager->updateDocumentTitle($documentID, $title);
    }

    /**
     * Creates a new document folder
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $parentID the ID of the parent folder
     * @param integer $roomID
     * @param string $title
     * @return Response
     */
    public function actionCreateDocumentFolder($parentID, $roomID, $title = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->documentManager->createDocumentFolder($parentID, $roomID, $title);
    }

    /**
     * Set documents order
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return bool
     * @throws BadRequestHttpException
     */
    public function actionSetDocumentsOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$documentsHierarchy = Yii::$app->request->post('documentsHierarchy')) {
            throw new BadRequestHttpException('Invalid request');
        }

        return Document::setDocumentsOrder($documentsHierarchy);
    }

    /**
     * Deletes a particular document
     *
     * @param integer $documentID the ID of the model to be deleted
     * @return Response
     */
    public function actionDeleteDocument($documentID)
    {
        $model = Document::findOne($documentID);

        $this->documentManager->deleteDocument($model);

        if (Yii::$app->request->isAjax) {
            return true;
        } else {
            return $this->redirect(['documents', 'id' => $model->room->roomCompany->id]);
        }
    }

    /**
     * Download document
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param int $id
     * @throws NotFoundHttpException
     */
    public function actionDownloadDocument($id)
    {
        $query = Document::find()->andWhere(['id' => $id]);

        if (Yii::$app->user->can('user')) {
            $query->published();
        }

        if (!$model = $query->one()) {
            throw new NotFoundHttpException('File not found or not active.');
        }

        $this->documentManager->downloadDocument($model);
    }

    /**
     * Download all documents in one archive
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $roomID
     * @param string $idList
     * @return string
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionDownloadAllDocuments($roomID, $idList = '')
    {
        $model = $this->findModel($roomID);

        if ($idList) {
            $idList = explode(',', $idList);
        }

        return $this->documentManager->downloadAllDocuments($model, Yii::$app->user->identity->isBuyer(), $idList);
    }

    /**
     * Manage document folders
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $id
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionManageDocumentsTree($id)
    {
        $this->layout = 'manage-room';
        $this->detailedRoomModel = $this->findModel($id);

        if ($this->detailedRoomModel->room->section != DataroomModule::SECTION_COMPANIES) {
            throw new BadRequestHttpException('Invalid request');
        }

        // Create default folders for documents (if needed)
        Document::createRoomDocumentsFolders($this->detailedRoomModel->roomID);

        return $this->render('manage-documents-tree');
    }

    /**
     * Ajax upload action for kartik\widgets\FileInput
     */
    public function actionUploadImages($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = $this->findModel($id);
        $room = $model->room;

        $document = $this->documentManager->createRoomImage($room, 'imageFiles');

        return $document->id ? [
            'initialPreview' => [$document->getDocumentUrl()],
            'initialPreviewConfig' => [[
                'caption' => $document->getDocumentName(),
                'size' => $document->size,
                'url' => \yii\helpers\Url::to(['delete-images']),
                'key' => $document->id,
            ]],
        ] : ['error' => $document->getErrors()];
    }

    /**
     * Ajax delete action for kartik\widgets\FileInput
     */
    public function actionDeleteImages()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $key = Yii::$app->request->post('key');

        if ($key) {
            $this->documentManager->deleteDocumentById($key);
        }

        return [];
    }

    /**
     * Download CA file
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param int $roomID
     * @return $this
     * @throws NotFoundHttpException
     */
    public function actionDownloadCaDocument($roomID)
    {
        $detailedRoomModel = $this->findModel($roomID);

        if (!$model = $detailedRoomModel->getDocumentModel('ca')) {
            throw new NotFoundHttpException('File not found or not active.');
        }

        return $this->documentManager->downloadDocument($model);
    }

    /**
     * Finds the AbstractDetailedRoom model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AbstractDetailedRoom the loaded model
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

    /**
     * Finds Room model.
     *
     * @param  int $id AbstractDetailedRoom id
     * @return Room
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findRoomModel($id)
    {
        $modelClass = $this->modelClass;

        $model = $modelClass::findOne($id);

        if ($model) {
            return $model->room;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
