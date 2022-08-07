<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\ProposalRealEstate */

$this->title = $model->proposalID;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Proposal Real Estates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="proposal-real-estate-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->proposalID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->proposalID], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'proposalID',
            'documentID',
            'firstName',
            'lastName',
            'address',
            'email:email',
            'phone',
            'kbisID',
            'cniID',
            'balanceSheetID',
            'taxNoticeID',
        ],
    ]) ?>

</div>
