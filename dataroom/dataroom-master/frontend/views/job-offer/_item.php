<?php

use kartik\helpers\Html;
use common\helpers\DateHelper;
use common\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $model \common\models\JobOffer */

setlocale(LC_TIME, 'fr_FR.UTF-8');
?>

<div class="job-offer-item">
    <div class="job-offer-date">Date de démarrage: <i><?= strftime('%e %B %G', strtotime($model->startDate)) ?></i></div>
    
    <h4 class="job-offer-title"><?= $model->title ?></h4>

    <p class="job-offer-details"><?= nl2br(StringHelper::trimToSymbols($model->description, 300)) ?></p>
    <?= Html::a(Yii::t('app', 'Read more'), ['view', 'id' => $model->id], ['class' => 'job-offer-read-more']); ?>
</div>
