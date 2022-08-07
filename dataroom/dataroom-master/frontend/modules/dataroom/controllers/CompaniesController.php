<?php

namespace frontend\modules\dataroom\controllers;

use backend\modules\dataroom\models\RoomCompany;
use backend\modules\dataroom\models\search\RoomCompanySearch;
use backend\modules\dataroom\models\RoomAccessRequestCompany;
use backend\modules\dataroom\models\ProposalCompany;
use backend\modules\document\models\Document;
use yii\web\NotFoundHttpException;

class CompaniesController extends AbstractRoomController
{
    protected $modelClass = RoomCompany::class;
    protected $searchModelClass = RoomCompanySearch::class;
    protected $accessRequestClass = RoomAccessRequestCompany::class;
    protected $proposalClass = ProposalCompany::class;

    protected $proposalFileName = 'Canevas_d_27offre_de_reprise';
}
