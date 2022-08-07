<?php

namespace backend\modules\dataroom\controllers\coownership;

use backend\modules\dataroom\controllers\AbstractRoomController;
use Yii;
use backend\modules\dataroom\models\search\ProposalCoownershipSearch;
use backend\modules\dataroom\models\RoomCoownership;
use backend\modules\dataroom\models\search\RoomCoownershipSearch;
use yii\web\Controller;

/**
 * RoomController implements the CRUD actions for RoomCoownership model.
 */
class RoomController extends AbstractRoomController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'AJAsyndic');
        $this->titleSmall = Yii::t('admin', 'Manage co-ownership offers');

        $this->modelClass = RoomCoownership::class;
        $this->searchModelClass = RoomCoownershipSearch::class;
        $this->proposalSearchModelClass = ProposalCoownershipSearch::class;

        parent::init();
    }
}
