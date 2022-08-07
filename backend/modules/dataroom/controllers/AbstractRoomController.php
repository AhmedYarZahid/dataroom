<?php

namespace backend\modules\dataroom\controllers;

use backend\modules\mailing\models\MailingCampaignSearch;
use backend\modules\notify\models\Notify;
use backend\modules\document\models\Document;
use backend\modules\document\models\DocumentSearch;
use backend\modules\document\models\DocumentHistory;
use common\components\managers\DocumentManager;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use backend\modules\dataroom\RoomManager;
use backend\modules\dataroom\exceptions\ManagerNotCreatedException;
use backend\modules\dataroom\models\AbstractDetailedRoom;
use backend\modules\dataroom\models\Room;
use backend\modules\dataroom\models\RoomHistory;
use backend\modules\dataroom\models\search\RoomAccessRequestSearch;
use yii\web\Response;
use backend\modules\dataroom\Module as DataroomModule;

abstract class AbstractRoomController extends \backend\controllers\Controller
{
    public $title = 'AJA Dataroom';

    /**
     * @var AbstractDetailedRoom current detailed room model for which we view data now (needed for navigation between room tabs)
     */
    public $detailedRoomModel;

    public $roomType;
    public $controllerID;

    protected $modelClass;
    protected $searchModelClass;
    protected $proposalSearchModelClass;

    protected $roomManager;
    protected $documentManager;

    public function __construct($id, $controller, RoomManager $roomManager, DocumentManager $documentManager, $config = [])
    {
        $this->roomManager = $roomManager;
        $this->documentManager = $documentManager;
        
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
     * Creates a room.
     */
    public function actionCreate()
    {
        $model = new $this->modelClass;
        $model->scenario = $model::SCENARIO_CREATE;

        $room = new Room;
        $room->scenario = $room::SCENARIO_CREATE;
        $room->userProfile = $model->dataroomSectionLabel;
        $room->createdDate = date('Y-m-d H:i:s');

        if (Yii::$app->user->identity->type == User::TYPE_ADMIN) {
            $room->adminID = Yii::$app->user->id;
        }

        try {
            $roomCreated = $room->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())
                && $this->roomManager->createRoom($room, $model);

            if ($roomCreated) {
                Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Room has been created successfully.'));

                if ($this->roomManager->newManagerCreated()) {
                    Notify::sendManagerCreated($room->user);
                }

                // Will be always one room for all types except for "CV Rooms" (can be several rooms created in one time)
                foreach ($this->roomManager->newDetailedRooms as $newDetailedRoom) {
                    Notify::sendRoomCreated($newDetailedRoom);
                }

                return $this->redirect(['index']);
            }
        } catch (ManagerNotCreatedException $e) {
            Yii::$app->getSession()->setFlash('error', Yii::t('admin', "There was an error creating a manager. Please try again."));
        } catch (Exception $e) {
            Yii::$app->getSession()->setFlash('error', Yii::t('admin', "There was an error creating a room. Please try again."));
        }
        
        return $this->render('create', [
            'model' => $model,
            'room' => $room,
        ]);
    }

    public function actionUpdate($id)
    {
        $this->layout = 'manage-room';

        $this->detailedRoomModel = $this->findModel($id);
        $detailedRoomClass = get_class($this->detailedRoomModel);
        $this->detailedRoomModel->scenario = $detailedRoomClass::SCENARIO_UPDATE;

        $room = $this->detailedRoomModel->room;
        $room->scenario = $room::SCENARIO_UPDATE;

        try {
            $updated = $room->load(Yii::$app->request->post()) && $this->detailedRoomModel->load(Yii::$app->request->post())
                && $this->roomManager->updateRoom($room, $this->detailedRoomModel);

            if ($updated) {
                Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Room has been updated successfully.'));

                return $this->redirect(['index']);
            }
        } catch (Exception $e) {
            Yii::$app->getSession()->setFlash('error', Yii::t('admin', "There was an error updating a room. Please try again."));
        }

        return $this->render('update', [
            'model' => $this->detailedRoomModel,
            'room' => $room,
        ]);
    }

    /**
     * List of proposals related to the room
     *
     * @param integer $id AbstractDetailedRoom id
     * @return string
     * @throws NotFoundHttpException
     * @throws NotSupportedException
     */
    public function actionProposals($id)
    {
        if (!$this->proposalSearchModelClass) {
            throw new NotSupportedException();
        }

        $this->layout = 'manage-room';

        $this->detailedRoomModel = $this->findModel($id);        
        $searchModel = new $this->proposalSearchModelClass;

        return $this->render('proposals', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->searchForRoom($this->detailedRoomModel->roomID, Yii::$app->request->queryParams),
        ]);
    }

    /**
     * Room stats.
     *
     * @param integer $id AbstractDetailedRoom id
     * @return string
     */
    public function actionStats($id)
    {
        $this->layout = 'manage-room';

        $this->detailedRoomModel = $this->findModel($id);
        $room = $this->detailedRoomModel->room;

        $accessRequestSearch = new RoomAccessRequestSearch;
        $accessRequestSearchProvider = $accessRequestSearch->searchForRoom($room->id, Yii::$app->request->queryParams);

        $roomHistory = new RoomHistory;
        $roomHistoryProvider = $roomHistory->searchForRoom($room->id, Yii::$app->request->queryParams);

        $docHistory = new DocumentHistory;
        $docHistoryProvider = $docHistory->searchForRoom($room->id, Yii::$app->request->queryParams);

        $mailingSearchModel = new MailingCampaignSearch();
        $queryParams = Yii::$app->request->queryParams;
        $queryParams['MailingCampaignSearch']['roomID'] = $this->detailedRoomModel->roomID;
        $mailingDataProvider = $mailingSearchModel->search($queryParams);

        $mailingStats = [];
        try {
            $mailingStats = Yii::$app->mailjet->getCampaignStats();
        } catch (Exception $e) {
            Yii::error($e->__toString(), 'mailing');
            Yii::$app->session->setFlash('warning', Yii::t('admin', "We couldn't load data from Mailjet. Please try to reload the page to get info about sent/unsent/opened emails."));
        }

        $forExport = Yii::$app->request->get('export');
        if ($forExport) {
            return $this->render('stats-export-mailing', [
                'mailingDataProvider' => $mailingDataProvider,
                'mailingSearchModel' => $mailingSearchModel,
                'mailingStats' => $mailingStats,
            ]);
        }

        return $this->render('stats', [
            'model' => $this->detailedRoomModel,
            'room' => $room,

            'accessRequestSearch' => $accessRequestSearch,
            'accessRequestSearchProvider' => $accessRequestSearchProvider,

            'roomHistory' => $roomHistory,
            'roomHistoryProvider' => $roomHistoryProvider,

            'docHistory' => $docHistory,
            'docHistoryProvider' => $docHistoryProvider,

            'mailingDataProvider' => $mailingDataProvider,
            'mailingSearchModel' => $mailingSearchModel,
            'mailingStats' => $mailingStats,
        ]);
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->detailedRoomModel->roomID);

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

        // Create default folders for documents (if needed)
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
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdateDocument($documentID)
    {
        $this->layout = 'manage-room';

        $model = Document::findOne($documentID);

        if ($model->isFolder || $model->type != Document::TYPE_ROOM) {
            throw new BadRequestHttpException('Invalid request.');
        }

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
            return $this->redirect(['documents', 'id' => $model->room->detailedRoom->id]);
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
     * @throws NotFoundHttpException
     * @throws \Exception
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
}
