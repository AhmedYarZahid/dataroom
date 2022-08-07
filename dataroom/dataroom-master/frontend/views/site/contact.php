<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backend\modules\contact\models\Contact */

$this->title = Yii::t('app', 'Contactez-nous');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact container">
    <?php if (!$type): ?>
        <p>
            Vous avez un dossier ouvert à notre étude, rendez-vous sur la <?= Html::a('page implantation', '/offices') ?> pour retrouver l’e-mail du collaborateur en charge de votre dossier.
        </p>

        <p>
            Vous souhaitez reprendre une entreprise en difficulté, rendez-vous sur la <?= Html::a('page dédiée', '/dataroom') ?>
        </p>

        <p>
            Vous n’êtes pas référencé et vous souhaitez des informations ou vous ne connaissez pas le collaborateur en charge de votre dossier, remplissez ce formulaire.
        </p>

        <p>
            Nous nous engageons à répondre à toutes les demandes dans un délai raisonnable sous réserve que le formulaire soit complet.
        </p>
    <?php endif ?>

    <?= $this->render('_contact-form', [
        'model' => $model,
        'submitted' => $submitted,
    ]) ?>

    <!-- This image and text will replace image and text in header using js -->
    <div class="tp-content-block header hidden">
        <?= Html::img('/images/tmp/contactez-nous-banner.jpg', ['class' => 'bg-image']) ?>
        <div class="heading-text"><?= $this->title ?></div>
    </div>
</div>
