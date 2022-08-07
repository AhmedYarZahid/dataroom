<?php

namespace backend\modules\metatags;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use \backend\modules\metatags\models\MetaTags;

class MetaTagsBehavior extends Behavior
{
    public $nodeType;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'saveMetaTags',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveMetaTags',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteMetaTags',
        ];
    }

    public function saveMetaTags()
    {
        $model = MetaTags::findModel($this->nodeType, $this->owner->getPrimaryKey());

        if (!$model) {
            $model = new MetaTags();
            $model->loadDefaultValues();
            $model->nodeType = $this->nodeType;
        }

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->nodeID) {
                $model->nodeID = $this->owner->getPrimaryKey();
            }

            $model->save();
        }
    }

    public function deleteMetaTags()
    {
        $model = MetaTags::findModel($this->nodeType, $this->owner->getPrimaryKey());

        if ($model) {
            $model->delete();
        }
    }
}