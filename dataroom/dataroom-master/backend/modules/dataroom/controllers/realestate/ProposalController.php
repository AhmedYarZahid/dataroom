<?php

namespace backend\modules\dataroom\controllers\realestate;

use Yii;
use backend\modules\dataroom\models\ProposalRealEstate;
use backend\modules\dataroom\models\search\ProposalRealEstateSearch;
use backend\modules\dataroom\controllers\AbstractProposalController;

class ProposalController extends AbstractProposalController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'AJArealestate');
        $this->titleSmall = Yii::t('admin', 'Manage real estate offers');

        $this->modelClass = ProposalRealEstate::class;
        $this->searchModelClass = ProposalRealEstateSearch::class;

        $this->proposalFileName = 'Canevas_d_27offre_de_reprise';

        parent::init();
    }
}
