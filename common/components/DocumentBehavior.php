<?php

namespace common\components;

use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\web\UploadedFile;
use backend\modules\document\models\Document;

/**
 * function behaviors()
 * {
 *     return [
 *         [
 *             'class' => DocumentBehavior::className(),
 *             'attributes' => ['regularFile' => Document::TYPE_REGULAR],
 *             'scenarios' => ['insert', 'update'],
 *         ],
 *     ];
 * }
 */
class DocumentBehavior extends Behavior
{
    public $attributes = [];
    public $scenarios = [];

    protected $files = [];

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            //BaseActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            //BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function beforeValidate()
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;

        if (in_array($model->scenario, $this->scenarios) || empty($this->scenarios)) {
            foreach ($this->attributes as $key => $value) {
                $this->getUploadedFile($key);
            }
        }
    }

    public function beforeSave()
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        if (in_array($model->scenario, $this->scenarios) || empty($this->scenarios)) {
            foreach ($this->attributes as $key => $value) {
                $this->saveDocument($key, $value);
            }
        }
    }

    public function afterDelete()
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;

        foreach ($this->attributes as $key => $value) {
            $this->deleteDocument($model->$key);
        }
    }

    public function getDocumentModel($attribute)
    {
        $model = $this->owner;

        if (!empty($this->attributes[$attribute]) && is_numeric($model->$attribute)) {
            $document = Document::findOne($model->$attribute);
            return $document;
        }

        return null;
    }

    public function getDocumentUrl($attribute)
    {
        $document = $this->getDocumentModel($attribute);

        return $document ? $document->getDocumentUrl() : null;
    }

    protected function getUploadedFile($attribute)
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;

        if (($file = $model->getAttribute($attribute)) instanceof UploadedFile) {
            $this->files[$attribute] = $file;
        } else {
            $this->files[$attribute] = UploadedFile::getInstance($model, $attribute);
        }

        if ($this->files[$attribute] instanceof UploadedFile) {
            $model->setAttribute($attribute, $this->files[$attribute]);
        } else {
            $oldDocumentID = $model->getOldAttribute($attribute);

            // If no new document was uploaded.
            if ($oldDocumentID && !$model->$attribute) {
                $model->setAttribute($attribute, $oldDocumentID);
            }  
        }
    }

    protected function saveDocument($attribute, $documentType)
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;

        $oldDocumentID = $model->getOldAttribute($attribute);  

        if ($model->$attribute instanceof UploadedFile) {
            $document = Yii::$app->documentManager->createFromModel($model, $attribute, $documentType);

            if ($document) {
                // Delete old document
                if ($oldDocumentID) {
                    $this->deleteDocument($oldDocumentID);
                }
                
                $model->setAttribute($attribute, $document->id);
            }
        }
    }

    /**
     * Deletes Document model and removes file from file system.
     * 
     * @param  int $id Document model id
     */
    protected function deleteDocument($id)
    {
        $document = Document::findOne($id);

        if ($document && $document->delete()) {
            $document->setOldAttribute('filePath', $document->filePath);
            $document->removeOldDocument();
        }
    }
}