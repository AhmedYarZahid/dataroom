<?php

namespace backend\modules\comments;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use \backend\modules\comments\models\CommentBundle;

class WithCommentsBehavior extends Behavior
{
    public $nodeType;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'saveCommentBundle',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveCommentBundle',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteCommentBundle',
        ];
    }

    public function saveCommentBundle()
    {
        $model = CommentBundle::findModel($this->nodeType, $this->owner->getPrimaryKey());

        if (!$model) {
            $model = new CommentBundle();
            $model->loadDefaultValues();
            $model->nodeType = $this->nodeType;
        }

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->nodeID) {
                $model->nodeID = $this->owner->getPrimaryKey();
            }

            if (isset($this->owner->title)) {
                $model->nodeTitle = $this->owner->title;
            }

            $model->save();
        }
    }

    public function deleteCommentBundle()
    {
        $model = CommentBundle::findModel($this->nodeType, $this->owner->getPrimaryKey());

        if ($model) {
            $model->delete();
        }
    }
}