<?php

namespace backend\modules\dataroom\controllers\coownership;

use Yii;
use backend\modules\dataroom\controllers\AbstractAccessRequestController;
use backend\modules\dataroom\models\RoomAccessRequestCoownership;
use backend\modules\dataroom\models\search\RoomAccessRequestCoownershipSearch;

/**
 * AccessRequestController implements the CRUD actions for RoomAccessRequestCoownership model.
 */
class AccessRequestController extends AbstractAccessRequestController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'AJAsyndic');
        $this->titleSmall = Yii::t('admin', 'Manage co-ownership offers');

        $this->modelClass = RoomAccessRequestCoownership::class;
        $this->searchModelClass = RoomAccessRequestCoownershipSearch::class;

        parent::init();
    }
}
