<?php

namespace frontend\modules\dataroom\controllers;

use backend\modules\dataroom\models\ProposalCoownership;
use backend\modules\dataroom\models\RoomAccessRequestCoownership;
use backend\modules\dataroom\models\RoomCoownership;
use backend\modules\dataroom\models\search\RoomCoownershipSearch;

class CoownershipController extends AbstractRoomController
{
    protected $modelClass = RoomCoownership::class;
    protected $searchModelClass = RoomCoownershipSearch::class;
    protected $accessRequestClass = RoomAccessRequestCoownership::class;
    protected $proposalClass = ProposalCoownership::class;

    protected $proposalFileName = 'Canevas_d_27offre_de_reprise';
}