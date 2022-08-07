<?php

namespace backend\modules\dataroom\controllers\cv;

use Yii;
use backend\modules\dataroom\controllers\AbstractAccessRequestController;
use backend\modules\dataroom\models\RoomAccessRequestCV;
use backend\modules\dataroom\models\search\RoomAccessRequestCVSearch;

/**
 * AccessRequestController implements the CRUD actions for RoomAccessRequestCV model.
 */
class AccessRequestController extends AbstractAccessRequestController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'AJAreclassement');
        $this->titleSmall = Yii::t('admin', 'Manage CV offers');

        $this->modelClass = RoomAccessRequestCV::class;
        $this->searchModelClass = RoomAccessRequestCVSearch::class;

        parent::init();
    }
}
