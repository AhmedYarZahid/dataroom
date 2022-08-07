<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$title = $this->title = $model->room->title;

if (!$model->room->isPublished()) {
    $titlePrefix = "[{$model->room->statusLabel()}] ";
    if ($model->room->isExpired()) {
        $titlePrefix = '<p class="room-expired-title-prefix">[OFFRE EXPIREE – vous rapprochez du contact procédure indiqué ci-dessous.]</p>';
    }
    $title = $titlePrefix . $this->title;
}
?>
<div class="container">

        <?php if(isset($_SERVER['HTTP_REFERER']) && strpos("?", $_SERVER['HTTP_REFERER'])) :  ?>
            <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn btn-primary btn-xs">retour aux résultats</a>
        <?php else: ?>
            <a href="<?php echo Url::toRoute(['/dataroom']); ?>" class="btn btn-primary btn-xs">retour aux résultats</a>
        <?php endif; ?>
</div>
<div class="page-offer-synthesis container">
    <div class="row">
        <div class="block-half block-half_left col-lg-6">
            <h1 class="page-heading">

                <?php if (!$model->room->public) : ?>
                     <?= $title ?>
                <?php else : ?>
                    <?php echo 'Mandat confidentiel'; ?>
                <?php endif; ?>
            </h1>
        </div>
        <div class="block-half block-half_right col-lg-6 text-center">
            <?php if (!$model->room->public && isset($model->room->images[0])) : ?>
                    <img src="<?php echo Yii::$app->urlManagerBackend->hostinfo. '/uploads/documents/' . $model->room->images[0]['filePath']; ?>" class="dataroom-list-img">
                    <?php else : ?>
                        <i class="fa fa-camera fa-5x"></i>
                    <?php endif; ?>
        </div>
    </div>
    <?php if ($model->room->isExpired() && $model->room->proposalsAllowed) : ?>
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
                <li><strong>Activité</strong> : <?= $model->activity ?></li>
                <li><strong>Région</strong> : <?= $model->region ?></li>
                <li><strong>Effectif</strong> : <?= $model->contributors ?></li>
                <li><strong>CA annuel (K€)</strong> : <?= number_format ( $model->annualTurnover, 0 , "," , " " ) ?></li>
            </ul>

            <div class="description">
                <strong>Description :</strong> <?= nl2br($model->desc) ?>
            </div>
        </div>

        <div class="block-half block-half_right col-lg-6">

            <h3 class="block-heading">
                Procédure
                <?php if ($nextRoom) : ?>
                    <?= Html::a('&#9656;', ['view-room', 'id' => $nextRoom->id], ['class' => 'next-offer-link']) ?>
                <?php endif ?>
            </h3>

            <ul class="info-list">
                <li><strong>Nature procédure</strong> : <?= $model->procedureNature ?></li>
                <li><strong>Date de désignation</strong> : <?= $model->designationDate ? $model->designationDate->format('d/m/Y') : '' ?></li>
                <li><strong>Contact procédure</strong> :<br><?= nl2br($model->procedureContact) ?></li>
            </ul>

            <ul class="contact-block info-list">
                <li><strong>Date limite de dépot des offres</strong> : <?= $model->room->expirationDate->format('d/m/Y H:i:s') ?></li>
                <?php /*
                <li><strong>Collaborateur</strong> : Céline PELZER</li>
                <li><strong>Ligne directe</strong> : 01-39-50-19-20</li>
                <li><strong>Email</strong> : c.pelzer@ajassocies.fr</li>
                */ ?>
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
            <?php elseif ($user->hasRefusedAccessRequest($model->room)) : ?>
                <a class="pull-left disabled btn btn-block btn-danger">Accès refusé</a>
            <?php endif ?>

            <?php /*
            <?php if (Yii::$app->user->can('makeProposal', ['room' => $model->room])) : ?>
                <a class="ask-for-file-access pull-left" href="<?= Url::to(['proposal', 'id' => $model->room->id]) ?>">
                    Faire une proposition
                </a>
            <?php elseif ($user->hasProposal($model->room)) : ?>
                <a class="ask-for-file-access pull-left disabled">Proposition envoyée</a>
            <?php elseif (Yii::$app->user->can('askForAccess', ['room' => $model->room])) : ?>
                <a class="ask-for-file-access pull-left" href="<?= Url::to(['get-access', 'id' => $model->room->id]) ?>">
                    Je demande l'accès au dossier
                </a>
            <?php elseif ($user->hasPendingAccessRequest($model->room)) : ?>
                <a class="ask-for-file-access pull-left disabled">Nous vérifions actuellement votre demande d'accès</a>
            <?php endif ?>

            <?= Html::a('Voir toutes nos offres', ['/dataroom/companies'], ['class' => 'view-all-offers pull-right']) ?>
            */ ?>
        </div>
    </div>
</div>