<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model \backend\modules\news\models\News */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Job Offers'), 'url' => ['/job-offer']];
$this->params['breadcrumbs'][] = $this->title;

setlocale(LC_TIME, 'fr_FR.UTF-8');
?>

<div class="job-offers-view container">
    <div class="block-header">

        <div class="job-offer-date">Date de démarrage: <i><?= strftime('%e %B %G', strtotime($model->startDate)) ?></i></div>

        <h2 class="job-offer-title"><?= $this->title ?></h2>
        <label>Type de contrat</label>
        <p class="job-offer-contract-type"><?= $model->contractType ?></p>
    </div>

    <div class="block-body">
        <label>Situation géographique (bureau)</label>
        <p><?= $model->location ?></p>
        <label>Description</label>
        <p><?= nl2br($model->description) ?></p>
        <label>Compétences</label>
        <p><?= nl2br($model->skills) ?></p>
        <label>Salaire</label>
        <p><?= $model->salary ?></p>
    </div>

    <p>
        <?= Html::a('Postuler', ['/site/contact-resume', 'id' => $model->id], ['class' => 'btn btn-default apply-to-the-job']) ?>
    </p>
</div>
