<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\dataroom\models\RoomCoownership;

/* @var $model \backend\modules\dataroom\models\RoomCV */

$title = $this->title = $model->room->title;

if (!$model->room->isPublished()) {
    $titlePrefix = "[{$model->room->statusLabel()}] ";
    if ($model->room->isExpired()) {
        $titlePrefix = '<p class="room-expired-title-prefix">[OFFRE EXPIREE – vous rapprochez du contact procédure indiqué ci-dessous.]</p>';
    }
    $title = $titlePrefix . $this->title;
}
?>

<div class="page-offer-synthesis container">
    <h1 class="page-heading">
        <?= $title ?>
    </h1>

    <?php if ($model->room->isExpired() && $model->room->proposalsAllowed): ?>
        <div>L’offre étant expirée, vous pouvez effectuer une offre de reprise, cependant nous ne garantissons pas de pouvoir la soumettre.</div>
    <?php endif ?>    

    <div class="row">
        <div class="block-half block-half_left col-lg-6">
            <h3 class="block-heading">
                <?php if ($prevRoom) : ?>
                    <?= Html::a('&#9664;', ['view-room', 'id' => $prevRoom->id], ['class' => 'prev-offer-link']) ?>
                <?php endif ?>
                Informations
            </h3>
            <ul class="info-list">
                <li><strong><?= $model->getAttributeLabel('companyName') ?></strong> : <?= $model->companyName ?></li>
                <li><strong><?= $model->getAttributeLabel('activityDomainID') ?></strong> : <?= $model->getActivityDomainName() ?></li>
                <li><strong><?= $model->getAttributeLabel('functionID') ?></strong> : <?= $model->getFunctionName() ?></li>
                <li><strong><?= $model->getAttributeLabel('regionID') ?></strong> : <?= $model->getRegionName() ?></li>
            </ul>

            <div class="description">
                <strong><?= $model->getAttributeLabel('candidateProfile') ?> :</strong> <?= nl2br($model->candidateProfile) ?>
            </div>
        </div>

        <div class="block-half block-half_right col-lg-6">
            
            <h3 class="block-heading">
                <?= Yii::t('app', 'Other info') ?>
                <?php if ($nextRoom) : ?>
                    <?= Html::a('&#9656;', ['view-room', 'id' => $nextRoom->id], ['class' => 'next-offer-link']) ?>
                <?php endif ?>       
            </h3>

            <ul class="contact-block info-list">
                <li><strong>Date limite de dépot des offres</strong> : <?= $model->room->expirationDate->format('d/m/Y H:i:s') ?></li>
            </ul>
            
            <?php if (Yii::$app->user->can('askForAccess', ['room' => $model->room])) : ?>
                <?php if (Yii::$app->user->isGuest) : ?>
                    <a class="ask-for-file-access pull-left" href="<?= Url::to(['/login', 'redirect' => Url::to(['get-access', 'id' => $model->id])]) ?>">
                        Je demande l'accès au dossier
                    </a>
                <?php else : ?>
                    <a class="ask-for-file-access pull-left" href="<?= Url::to(['get-access', 'id' => $model->id]) ?>">
                        Je demande l'accès au dossier
                    </a>
                <?php endif ?>
            <?php elseif ($user->hasPendingAccessRequest($model->room)) : ?>
                <a class="ask-for-file-access pull-left disabled">Nous vérifions actuellement votre demande d'accès</a>
            <?php endif ?>
        </div>
    </div>
</div>