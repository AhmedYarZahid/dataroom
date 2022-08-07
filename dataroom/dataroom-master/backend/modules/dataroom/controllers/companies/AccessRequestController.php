<?php

namespace backend\modules\dataroom\controllers\companies;

use backend\modules\dataroom\controllers\AbstractAccessRequestController;
use backend\modules\dataroom\models\RoomAccessRequestCompany;
use backend\modules\dataroom\models\search\RoomAccessRequestCompanySearch;

class AccessRequestController extends AbstractAccessRequestController
{
    public $title = 'AJArepreneurs';
    public $titleSmall = "Gérer les offres de reprise d'entreprise";

    protected $modelClass = RoomAccessRequestCompany::class;
    protected $searchModelClass = RoomAccessRequestCompanySearch::class;
}
