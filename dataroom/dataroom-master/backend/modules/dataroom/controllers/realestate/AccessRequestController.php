<?php

namespace backend\modules\dataroom\controllers\realestate;

use Yii;
use backend\modules\dataroom\models\RoomAccessRequestRealEstate;
use backend\modules\dataroom\models\search\RoomAccessRequestRealEstateSearch;
use backend\modules\dataroom\controllers\AbstractAccessRequestController;

class AccessRequestController extends AbstractAccessRequestController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'AJArealestate');
        $this->titleSmall = Yii::t('admin', 'Manage real estate offers');

        $this->modelClass = RoomAccessRequestRealEstate::class;
        $this->searchModelClass = RoomAccessRequestRealEstateSearch::class;

        parent::init();
    }
}
