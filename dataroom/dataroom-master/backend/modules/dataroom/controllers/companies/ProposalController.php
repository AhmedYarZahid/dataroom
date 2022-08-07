<?php

namespace backend\modules\dataroom\controllers\companies;

use backend\modules\dataroom\controllers\AbstractProposalController;
use backend\modules\dataroom\models\ProposalCompany;
use backend\modules\dataroom\models\search\ProposalCompanySearch;

class ProposalController extends AbstractProposalController
{
    public $title = 'AJArepreneurs';
    public $titleSmall = "Gérer les offres de reprise d'entreprise";

    protected $proposalFileName = 'Canevas_d_27offre_de_reprise';

    protected $modelClass = ProposalCompany::class;
    protected $searchModelClass = ProposalCompanySearch::class;
}
