<?php

namespace frontend\modules\dataroom\controllers;

use backend\modules\dataroom\models\RoomRealEstate;
use backend\modules\dataroom\models\search\RoomRealEstateSearch;
use backend\modules\dataroom\models\RoomAccessRequestRealEstate;
use backend\modules\dataroom\models\ProposalRealEstate;

class RealEstateController extends AbstractRoomController
{
    protected $modelClass = RoomRealEstate::class;
    protected $searchModelClass = RoomRealEstateSearch::class;
    protected $accessRequestClass = RoomAccessRequestRealEstate::class;
    protected $proposalClass = ProposalRealEstate::class;

    protected $proposalFileName = 'Canevas_d_27offre_de_reprise';
}
