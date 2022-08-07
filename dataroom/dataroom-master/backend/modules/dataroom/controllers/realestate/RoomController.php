<?php

namespace backend\modules\dataroom\controllers\realestate;

use Yii;
use backend\modules\dataroom\models\search\ProposalRealEstateSearch;
use backend\modules\dataroom\controllers\AbstractRoomController;
use backend\modules\dataroom\models\RoomRealEstate;
use backend\modules\dataroom\models\search\RoomRealEstateSearch;

class RoomController extends AbstractRoomController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'AJArealestate');
        $this->titleSmall = Yii::t('admin', 'Manage real estate offers');

        $this->modelClass = RoomRealEstate::class;
        $this->searchModelClass = RoomRealEstateSearch::class;
        $this->proposalSearchModelClass = ProposalRealEstateSearch::class;

        parent::init();
    }
}
