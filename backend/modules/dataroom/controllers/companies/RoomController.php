<?php

namespace backend\modules\dataroom\controllers\companies;

use backend\modules\dataroom\controllers\AbstractRoomController;
use backend\modules\dataroom\models\RoomCompany;
use backend\modules\dataroom\models\search\RoomCompanySearch;
use backend\modules\dataroom\models\search\ProposalCompanySearch;

class RoomController extends AbstractRoomController
{
    public $title = 'AJArepreneurs';
    public $titleSmall = "Gérer les offres de reprise d'entreprise";

    protected $modelClass = RoomCompany::class;
    protected $searchModelClass = RoomCompanySearch::class;
    protected $proposalSearchModelClass = ProposalCompanySearch::class;
}
