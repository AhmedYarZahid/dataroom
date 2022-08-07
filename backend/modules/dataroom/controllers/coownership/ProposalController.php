<?php

namespace backend\modules\dataroom\controllers\coownership;

use backend\modules\dataroom\controllers\AbstractProposalController;
use Yii;
use backend\modules\dataroom\models\ProposalCoownership;
use backend\modules\dataroom\models\search\ProposalCoownershipSearch;

/**
 * ProposalController implements the CRUD actions for ProposalCoownership model.
 */
class ProposalController extends AbstractProposalController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'AJAsyndic');
        $this->titleSmall = Yii::t('admin', 'Manage co-ownership offers');

        $this->modelClass = ProposalCoownership::class;
        $this->searchModelClass = ProposalCoownershipSearch::class;

        $this->proposalFileName = 'Canevas_d_27offre_de_reprise';

        parent::init();
    }
}
