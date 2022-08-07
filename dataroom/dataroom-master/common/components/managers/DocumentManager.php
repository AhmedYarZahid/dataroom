<?php

namespace common\components\managers;

use backend\modules\dataroom\models\AbstractDetailedRoom;
use backend\modules\dataroom\models\RoomCompany;
use backend\modules\notify\models\Notify;
use Yii;
use yii\base\Component;
use yii\web\Response;
use yii\web\UploadedFile;
use common\helpers\FileHelper;
use backend\modules\document\models\Document;
use backend\modules\document\models\DocumentHistory;
use backend\modules\dataroom\models\Room;

class DocumentManager extends Component
{
    public function createFromContact($model, $attribute, $type)
    {
        $document = $this->prepareFromModel($model, $attribute, $type);

        if ($document) {
            $document->contactID = $model->id;
            $document->save(false);
            return $document;
        }
        
        return null;
    }

    public function createRoomImage(Room $room, $attribute)
    {
        $document = $this->prepareFromModel($room, $attribute, Document::TYPE_ROOM_IMAGE);

        if ($document) {
            $document->roomID = $room->id;
            $document->save(false);
            return $document;
        }
        
        return null;
    }

    public function createFromModel($model, $attribute, $type)
    {
        $document = $this->prepareFromModel($model, $attribute, $type);
        
        if ($document) {
            if ($model instanceof AbstractDetailedRoom) {
                $document->roomID = $model->roomID;
            }
            $document->save(false);
        }

        return $document;
    }

    public function deleteDocumentById($id)
    {
        $document = Document::findOne($id);

        if ($document) {
            $document->deleteFiles();
            $document->delete();
        }
    }

    public function trackDownload(Document $document, $addHistory = true)
    {
        if ($addHistory) {
            $user = !Yii::$app->user->isGuest ? Yii::$app->user->identity : null;
            DocumentHistory::addRecord($document, $user);
        }

        $document->downloads++;
        $document->save(false);
    }

    public function trackArchiveDownload(Room $room)
    {
        $user = !Yii::$app->user->isGuest ? Yii::$app->user->identity : null;
        DocumentHistory::addArchiveRecord($room, $user);
    }

    protected function prepareFromModel($model, $attribute, $type)
    {
        if ($model->$attribute instanceof UploadedFile) {
            $file = $model->$attribute;
        } else {
            $file = UploadedFile::getInstance($model, $attribute);    
        }

        if (!$file) {
            return;
        }

        $document = new Document;
        $document->scenario = 'add-document';

        $filePath = FileHelper::getStorageStructure(Yii::getAlias('@uploads/documents/')) . trim($file->baseName) . '.' . $file->extension;
        $fileSaved = $file->saveAs(Yii::getAlias('@uploads/documents/') . $filePath);
        
        if (!$fileSaved) {
            return;
        }

        $document->type = $type;
        $document->title = trim($file->baseName);
        $document->size = $file->size;
        $document->comment = '';
        $document->filePath = $filePath;
        $document->publishDate = date('Y-m-d');
        $document->isActive = 1;

        return $document;
    }

    /**
     * Creates room document
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param Document $model
     * @param integer $roomID
     * @return bool
     */
    public function createRoomDocument(Document $model, $roomID)
    {
        $model->filePath = UploadedFile::getInstance($model, 'filePath');

        if (empty($model->title) && $model->filePath) {
            $model->title = trim($model->filePath->baseName);
        }

        $model->roomID = $roomID;
        $model->type = Document::TYPE_ROOM;

        if ($model->validate()) {
            $model->saveUploadedDocument();

            if ($model->save(false)) {
                //Send notifications
                Notify::sendDocumentsUploadedToRoom([$model]);

                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * Updates room document
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param Document $model
     * @param string $oldFilePath
     * @return bool
     */
    public function updateRoomDocument(Document $model, $oldFilePath)
    {
        if ($model->validate()) {
            $documentUploaded = $model->saveUploadedDocument();

            if ($model->save(false)) {

                if ($documentUploaded) {
                    $model->setOldAttribute('filePath', $oldFilePath);
                    $model->removeOldDocument();
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Download all documents in one archive
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param AbstractDetailedRoom $model
     * @param bool $onlyPublished
     * @param array $idList
     * @return $this
     * @throws \Exception
     * @throws \yii\base\Exception
     */
    public function downloadAllDocuments(AbstractDetailedRoom $model, $onlyPublished = false, $idList = array())
    {
        $documents = $onlyPublished ? $model->room->publishedDocuments : $model->room->documents;

        // Choose only specified documents
        if ($idList) {
            foreach ($documents as $key => $document) {
                if (!in_array($document->id, $idList)) {
                    unset($documents[$key]);
                }
            }
        }

        if (!count($documents)) {
            echo 'No documents found.';
            return false;
        }

        $tmpDir = Yii::getAlias(Document::TMP_ARCHIVE_FOLDER . '/') . Yii::$app->security->generateRandomString(12);
        FileHelper::createDirectory($tmpDir);

        $zipFileName = 'documents_room_' . $model->room->id . '.zip';
        $zipFilePath = $tmpDir . '/' . $zipFileName;

        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE) !== true) {
            throw new \Exception('Cannot create a zip file.');
        }

        foreach ($documents as $document) {
            $zip->addFile($document->getDocumentPath(), $document->title . '.' . pathinfo($document->getDocumentPath(), PATHINFO_EXTENSION));

            $this->trackDownload($document, false);
        }

        $this->trackArchiveDownload($model->room);

        $zip->close();

        Yii::$app->response->on(Response::EVENT_AFTER_SEND, function($event) { FileHelper::removeDirectory($event->data); }, $tmpDir);

        return Yii::$app->response->sendFile($zipFilePath);
    }

    /**
     * Delete document
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param Document $model
     * @return false|int
     * @throws \Exception
     * @throws \Throwable
     */
    public function deleteDocument(Document $model)
    {
        $model->deleteFiles();

        return $model->delete();
    }

    /**
     * Create document folder
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $parentID
     * @param integer $roomID
     * @param string $title
     * @return bool|int
     */
    public function createDocumentFolder($parentID, $roomID, $title = '')
    {
        if (!$title) {
            $title = Yii::t('admin', 'New folder');
        }

        if (!is_numeric($parentID)) {
            $parentID = null;
        }

        $model = new Document();
        $model->title = $title;
        $model->parentID = $parentID;
        $model->roomID = $roomID;
        $model->isFolder = 1;
        $model->isActive = 1;
        $model->type = Document::TYPE_ROOM;
        $model->filePath = '';

        if ($model->save(true, ['title', 'parentID', 'roomID', 'isFolder', 'isActive', 'type', 'filePath'])) {
            return $model->id;
        } else {
            return false;
        }
    }

    /**
     * Update document title
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $documentID
     * @param $title
     * @return bool
     */
    public function updateDocumentTitle($documentID, $title)
    {
        $model = Document::findOne($documentID);
        $model->scenario = 'update';

        $model->title = $title;

        return $model->save(true, ['title']);
    }

    /**
     * Create multiple documents
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $model
     * @return bool
     */
    public function createMultipleDocuments($model, $roomID)
    {
        $model->filePath = UploadedFile::getInstances($model, 'filePath');

        if ($model->validate()) {
            $documentModels = [];
            foreach ($model->filePath as $file) {
                $documentModel = new Document();
                $documentModel->title = trim($file->baseName);
                $documentModel->parentID = $model->parentID;
                $documentModel->roomID = $roomID;
                $documentModel->type = Document::TYPE_ROOM;
                $documentModel->comment = $model->comment;
                $documentModel->publishDate = $model->publishDate;
                $documentModel->isActive = $model->isActive;

                $documentModel->saveUploadedDocument($file);

                $documentModel->save(false);

                $documentModels[] = $documentModel;
            }

            //Send notifications
            Notify::sendDocumentsUploadedToRoom($documentModels);

            return true;
        }

        return false;
    }

    /**
     * Send document to browser
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param Document $model
     * @return $this
     */
    public function downloadDocument(Document $model)
    {
        $this->trackDownload($model);

        return Yii::$app->response->sendFile($model->getDocumentPath(), $model->title . '.' . pathinfo($model->getDocumentPath(), PATHINFO_EXTENSION));
    }
}