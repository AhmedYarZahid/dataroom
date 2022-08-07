<?php

namespace backend\modules\document\models;

use backend\modules\dataroom\models\Room;
use common\helpers\BrowserHelper;
use common\models\User;
use common\helpers\FileHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\UploadedFile;
use backend\modules\dataroom\Module as DataroomModule;

/**
 * This is the model class for table "Document".
 *
 * @property integer $id
 * @property string $title
 * @property integer $parentID
 * @property string $filePath
 * @property string $type
 * @property string $comment
 * @property string $publishDate
 * @property integer $isActive
 * @property integer $rank
 * @property integer $size
 * @property boolean $isFolder
 * @property integer $roomID
 * @property integer $contactID
 * @property string $updatedDate
 *
 * @property Room $room
 * @property DocumentHistory[] $documentHistories
 * @property DocumentHistory $lastDownload
 */
class Document extends ActiveRecord
{
    const TYPE_REGULAR = 'regular';
    const TYPE_CONTACT = 'contact';
    const TYPE_RESUME = 'resume';
    const TYPE_COVER_LETTER = 'cover_letter';
    const TYPE_PROPOSAL = 'proposal';
    const TYPE_ACCESS_REQUEST = 'access_request';
    const TYPE_ROOM = 'room';
    const TYPE_ROOM_IMAGE = 'room_image';
    const TYPE_ROOM_SPECIFIC = 'room_specific';

    const TMP_ARCHIVE_FOLDER = '@app/runtime/tmp-documents-archive';

    /**
     * Set default date to current and active to true
     * @since  v2.0.0
     */
    public function init()
    {
        parent::init();
        $this->isActive = 1;
        $this->publishDate = date('Y-m-d');
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Document';
    }

    /**
     * @inheritdoc
     * @return DocumentQuery
     */
    public static function find()
    {
        return new DocumentQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdDate',
                'updatedAtAttribute' => 'updatedDate',
                'value' => function () {
                    return date('Y-m-d H:i:s');
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    /*public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['add-document'] = ['filePath'];

        return $scenarios;
    }*/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required', 'on' => ['update', 'update-room-document', 'update-room-document-no-folder']],
            [['filePath'], 'required', 'on' => [
                'add-document', 'add-documents',
                'add-room-document', 'add-room-documents',
                'add-room-document-no-folder', 'add-room-documents-no-folder'
            ]],
            [['type'], 'string'],
            [['publishDate', 'updatedDate'], 'safe'],
            [['isActive', 'rank'], 'integer'],
            [['title'], 'string', 'max' => 70],
            [['comment'], 'string', 'max' => 250],
            [['filePath'], 'safe'],
            [['filePath'], 'file', 'extensions' => ['pdf', 'doc', 'docx', 'txt', 'jpg', 'png', 'gif'], 'except' => ['add-room-documents', 'add-room-documents-no-folder', 'add-documents']],
            [['filePath'], 'file', 'extensions' => ['pdf', 'doc', 'docx', 'txt', 'jpg', 'png', 'gif'], 'maxFiles' => 20, 'on' => ['add-room-documents', 'add-room-documents-no-folder', 'add-documents']],
            [['contactID', 'parentID', 'size'], 'integer'],
            [['parentID'], 'required', 'on' => ['add-room-document', 'add-room-documents', 'update-room-document']],
            ['rank', 'default', 'value' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('document', 'ID'),
            'title' => Yii::t('document', 'Title'),
            'filePath' => Yii::t('document', 'File(s)'),
            'type' => Yii::t('document', 'Type'),
            'comment' => Yii::t('document', 'Comment'),
            'publishDate' => Yii::t('document', 'Publish Date'),
            'isActive' => Yii::t('document', 'Active'),
            'rank' => Yii::t('document', 'Order'),
            'parentID' => Yii::t('document', 'Folder'),
            'size' => Yii::t('document', 'Size'),
            'updatedDate' => Yii::t('document', 'Updated Date'),
        ];
    }

    /**
     * Get default folders structure for each new room
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return array
     */
    protected static function getDefaultRoomDocumentsFolders()
    {
        return [
            Yii::t('admin', 'How to bid'),
            Yii::t('admin', 'Information about the company'),
            Yii::t('admin', 'Accounting documents'),
            Yii::t('admin', 'Assets'),
            Yii::t('admin', 'Liabilities and Articles'),
            Yii::t('admin', 'Contracts'),
            Yii::t('admin', 'Social'),
            Yii::t('admin', 'Environment and standards'),
            Yii::t('admin', 'Intellectual property'),
            Yii::t('admin', 'Miscellaneous'),
        ];
    }

    /**
     * Creates folders structure for room documents
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $roomID
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function createRoomDocumentsFolders($roomID)
    {
        if (Room::findOne($roomID)->section != DataroomModule::SECTION_COMPANIES) {
            return false;
        }

        if (!self::find()->where(['roomID' => $roomID, 'isFolder' => 1])->exists()) {
            $data = [];
            foreach (self::getDefaultRoomDocumentsFolders() as $rank => $folderName) {
                $data[] = [$folderName, '', $roomID, self::TYPE_ROOM, 1, ++$rank, date('Y-m-d H:i:s')];
            }

            if (!empty($data)) {
                Yii::$app->db->createCommand()->batchInsert('Document', ['title', 'filePath', 'roomID', 'type', 'isFolder', 'rank', 'createdDate'], $data)->execute();
            }
        }
    }

    /**
     * Get documents tree for room
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $roomID
     * @param integer $selected
     * @param bool $skipEmptyFolders
     * @param bool $onlyPublished
     * @param bool $addDatesInfo
     * @return array
     */
    public static function getRoomDocumentsTree($roomID, $selected = null, $skipEmptyFolders = false, $onlyPublished = false, $addDatesInfo = false)
    {
        $roomDocumentsList = self::getList(null, $onlyPublished, $roomID);

        return self::buildTree($roomDocumentsList, null, $selected, $skipEmptyFolders, $addDatesInfo);
    }

    /**
     * Build documents tree
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param Document[] $documentsList
     * @param integer $parentFolderID
     * @param integer $selectedID
     * @param bool $skipEmptyFolders
     * @param bool $addDatesInfo
     * @return array
     */
    private static function buildTree($documentsList, $parentFolderID, $selectedID = null, $skipEmptyFolders = false, $addDatesInfo = false)
    {
        $tree = [];
        foreach ($documentsList as $document) {

            if ($document->parentID == $parentFolderID) {

                if ($document->isFolder && $skipEmptyFolders && self::isFolderEmpty($document->id)) {
                    continue;
                }

                $childs = self::buildTree($documentsList, $document->id, $selectedID, $skipEmptyFolders, $addDatesInfo);

                $title = $document->title;
                if ($addDatesInfo && !$document->isFolder) {
                    $title .= ' <span class="document-info">(';
                    if ($document->lastDownload) {
                        $title .= Yii::t('app', 'downloaded on {date}', ['date' => Yii::$app->formatter->asDatetime($document->lastDownload->createdDate)]) . ', ';
                    }
                    $title .= Yii::t('app', 'added on {date}', ['date' => Yii::$app->formatter->asDatetime($document->createdDate)]) . ')</span>';
                }

                $tree[] = [
                    //'title' => $document->title . '.' . pathinfo($document->getDocumentPath(), PATHINFO_EXTENSION),
                    'title' => $title,
                    'key' => $document->id,
                    'folder' => $document->isFolder,
                    'expanded' => true,
                    'children' => $childs,
                    'selected' => ($document->id == $selectedID),
                    'unselectable' => $document->isFolder,
                    'downloadLink' => $document->getDocumentUrl(),
                    'updateLink' => Url::to(['update-document', 'documentID' => $document->id])
                ];
            }
        }

        return $tree;
    }

    /**
     * Check of folder doesn't contains documents
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $documentID
     * @return bool
     */
    private static function isFolderEmpty($documentID)
    {
        $childNodes = Document::find()->where(['parentID' => $documentID])->all();

        foreach ($childNodes as $node) {
            if (!$node->isFolder) {
                return false;
            }

            return self::isFolderEmpty($node->id);
        }

        return true;
    }

    /**
     * Move uploaded file to the correct folder
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param UploadedFile $file
     * @return bool
     */
    public function saveUploadedDocument($file = null)
    {
        if ($file || ($file = UploadedFile::getInstance($this, 'filePath'))) {
            $this->filePath = FileHelper::getStorageStructure(\Yii::getAlias('@uploads/documents/')) . Yii::$app->security->generateRandomString(25) . '.' . $file->extension;
            $this->size = $file->size;

            $file->saveAs(\Yii::getAlias('@uploads/documents/') . $this->filePath);

            return true;
        } else {
            $this->filePath = $this->getOldAttribute('filePath');
        }

        return false;
    }

    /**
     * Save archive to the correct folder
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $files
     * @return bool
     * @throws \Exception
     */
    public function saveArchive($files)
    {
        $this->filePath = FileHelper::getStorageStructure(\Yii::getAlias('@uploads/documents/')) . 'documents_' . Yii::$app->security->generateRandomString(20) . '.zip';
        $fullArchivePath = \Yii::getAlias('@uploads/documents/') . $this->filePath;

        $zip = new \ZipArchive();
        if ($zip->open($fullArchivePath, \ZipArchive::CREATE) !== true) {
            throw new \Exception('Cannot create a zip file.');
        }

        foreach ($files as $file) {
            $zip->addFile($file->tempName, $file->baseName . '.' . $file->getExtension());
        }

        $zip->close();

        $this->size = filesize($fullArchivePath);

        return true;
    }

    /**
     * Return full path to the file (if exists)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param bool $relative
     * @return string
     */
    public function getDocumentPath($relative = false)
    {
        $path = \Yii::getAlias('@uploads/documents/') . $this->filePath;

        if (!is_file($path)) {
            return '';
        } else {
            return $relative ? (\Yii::getAlias('@uploads/documents-rel/') . $this->filePath) : $path;
        }
    }

    /**
     * Get download link to document
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param bool $forAdmin
     * @return string
     */
    public function getDocumentUrl($forAdmin = false)
    {
        $path = \Yii::getAlias('@uploads/documents/') . $this->filePath;

        if (!is_file($path)) {
            return '';
        } else {
            if ($this->type == self::TYPE_REGULAR) {
                if ($forAdmin) {
                    return Yii::$app->urlManagerBackend->createAbsoluteUrl(['/document/manage/download', 'id' => $this->id]);
                } else {
                    return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/download-document', 'id' => $this->id]);
                }
            } else {
                if (Yii::$app->id == 'app-frontend') {
                    return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/dataroom/companies/download-document', 'id' => $this->id]);
                } else {
                    return Yii::$app->urlManagerBackend->createAbsoluteUrl(['/dataroom/companies/room/download-document', 'id' => $this->id]);
                }
            }
        }
    }

    public function getDocumentUrlFrontend()
    {
        $path = \Yii::getAlias('@uploads/documents/') . $this->filePath;

        if (!is_file($path)) {
            return '';
        } else {
            return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/frontend/web/document/download', 'id' => $this->id]);
        }
    }

    public function getDocumentName()
    {
        $ext = $this->getExtension();

        return $ext ? $this->title . '.' . $ext : $this->title;
    }

    public function getExtension()
    {
        $info = pathinfo($this->filePath);

        return isset($info['extension']) ? $info['extension'] : null;
    }

    /**
     * Remove old document
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function removeOldDocument()
    {
        $fullPath = \Yii::getAlias('@uploads/documents/') . $this->getOldAttribute('filePath');

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    /**
     * Get list of document types
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return array
     */
    public static function getTypesList()
    {
        return [
            self::TYPE_REGULAR => Yii::t('document', 'Regular'),
            self::TYPE_CONTACT => Yii::t('document', 'Contact'),
            self::TYPE_RESUME => Yii::t('document', 'Resume'),
            self::TYPE_COVER_LETTER => Yii::t('document', 'Cover Letter'),
            self::TYPE_ROOM => Yii::t('document', 'Room'),
            self::TYPE_ACCESS_REQUEST => Yii::t('document', 'Access request'),
            self::TYPE_PROPOSAL => Yii::t('document', 'Proposal'),
            self::TYPE_ROOM_IMAGE => Yii::t('document', 'Room image'),
            self::TYPE_ROOM_SPECIFIC => Yii::t('document', 'Room specific'),
        ];
    }

    /**
     * Get type name
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getTypeName()
    {
        $typesList = self::getTypesList();

        return $typesList[$this->type];
    }

    /**
     * Get "isPublished" flag (magic method)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return bool
     */
    public function getIsPublished()
    {
        return ($this->isActive && $this->publishDate && strtotime($this->publishDate) <= strtotime(date('Y-m-d', time())));
    }

    /**
     * Get documents list
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param int $limit
     * @param bool $published
     * @param integer $roomID
     * @return Document[]
     */
    public static function getList($limit = 50, $published = true, $roomID = null)
    {
        $query = Document::find();

        if ($limit) {
            $query->limit = $limit;
        }

        if ($published) {
            $query->published();
        }

        if ($roomID) {
            $query->roomFile($roomID);
        }

        $query->orderBy = ['rank' => SORT_ASC];

        //$query->orderBy = ['rank' => SORT_ASC, 'publishDate' => SORT_DESC];

        return $query->all();
    }

    /**
     * Remove file of current document and also child files
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return bool
     */
    public function deleteFiles()
    {
        $this->setOldAttribute('filePath', $this->filePath);
        $this->removeOldDocument();

        $childDocumentsList = Document::find()->where(['parentID' => $this->id])->all();
        foreach ($childDocumentsList as $document) {
            return $document->deleteFiles();
        }

        return true;
    }


    /**
     * Set documents order
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $documentsHierarchy
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function setDocumentsOrder($documentsHierarchy)
    {
        $position = 1;
        foreach ($documentsHierarchy as $documentData) {
            Yii::$app->db->createCommand()->update(self::tableName(), [
                'parentID' => is_numeric($documentData['parentID']) ? $documentData['parentID'] : null,
                'rank' => $position
            ], ['id' => $documentData['id']])->execute();

            if (!empty($documentData['children'])) {
                self::setDocumentsOrder($documentData['children']);
            }

            $position++;
        }

        return true;
    }

    public static function showUploadPreview()
    {
        return (new BrowserHelper())->getBrowser() != BrowserHelper::BROWSER_IE;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'roomID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentHistories()
    {
        return $this->hasMany(DocumentHistory::class, ['documentID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastDownload()
    {
        return $this->hasOne(DocumentHistory::class, ['documentID' => 'id'])->orderBy(['DocumentHistory.createdDate' => SORT_DESC]);
    }
}