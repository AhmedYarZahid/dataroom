<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Join us');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="job-offers-index container">
    <div class="job-offers-text">
        <p>
            L’effectif est axé autour de 5 catégories : comptables, techniciens, assistants, collaborateurs et stagiaires inscrits. Tous rejoignent une équipe à dimension humaine, soudée autour des valeurs d’AJAssociés.
        </p>

        <p>
            AJAssociés offre aux étudiants des possibilités de stage toute l'année. Les candidats sont sélectionnés en fonction de la qualité de leurs diplômes, de leur aptitude à s'intégrer dans une équipe compétente, dynamique et conviviale et de la cohérence entre notre métier et le cursus suivi.
        </p>

        <p>
            Vous répondez à une offre publiée :
        </p>

        <p>
            Communiquez-nous votre CV et lettre de motivation et précisez-nous vos disponibilités si nous devons vous solliciter pour un entretien, ainsi que le lieu du bureau concerné pour l’offre de stage
        </p>

        <p>
            Votre candidature est spontanée :
        </p>

        <p>
            Communiquez-nous votre CV et lettre de motivation, ainsi qu’un mail d’accompagnement dans lequel vous prendrez soin de préciser votre profil, vos dates de disponibilité, le ou les bureaux sur lesquels vous souhaitez postuler à l’adresse suivante : contact@ajassocies.fr
        </p>

        <p>
            Toutes les candidatures sont étudiées avec soin, dès lors que les qualités, compétences ou expériences requises se trouvent réunies.
        </p>
    </div>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => '_item',
        'summary' => '',
        /*'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model->id), ['view', 'id' => $model->id]);
        },*/
    ]) ?>

    <!-- This image and text will replace image and text in header using js -->
    <div class="tp-content-block header hidden">
        <?= Html::img('/images/tmp/job-offers-banner.jpg', ['class' => 'bg-image']) ?>
        <div class="heading-text"><?= $this->title ?></div>
    </div>

</div>
