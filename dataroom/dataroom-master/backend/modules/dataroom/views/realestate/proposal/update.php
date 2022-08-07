<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\ProposalRealEstate */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Proposal Real Estate',
]) . $model->proposalID;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Proposal Real Estates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->proposalID, 'url' => ['view', 'id' => $model->proposalID]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="proposal-real-estate-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
