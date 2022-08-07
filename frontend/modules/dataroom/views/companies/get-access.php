<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;

$this->title = $roomModel->room->title;

$fileInputOptions = [
    'options' => [
        //'accept' => 'application/pdf',
        'multiple' => false
    ],
    'pluginOptions' => [
        'previewFileType' => 'any',
        'showPreview' => false,
        'showCaption' => true,
        'showRemove' => false,
        'showUpload' => false,
        'allowedFileExtensions' => ['pdf','doc','docx','txt','jpg','jpeg','gif','png'],
        'browseClass' => 'btn btn-primary btn-block',
        'removeClass' => 'btn btn-default btn-remove',
    ],
];
?>

<div class="user-get-access container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="content-header col-md-6">
            <div>
                <h1><?= Html::encode($this->title) ?></h1>
            </div>
            <div>
                <a class="btn view-room-btn" href="<?= Url::to(['view-room', 'id' => $roomModel->id]) ?>">
                    <?= Yii::t('app', 'View room') ?>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <?php $form = ActiveForm::begin([
                //'enableClientValidation' => false,
                //'validateOnSubmit' => false,
                'options' => ['enctype' => 'multipart/form-data'],
            ]); ?>

            <?php if (!$user->id) : ?>
                <?= $this->render('/user/_register_form', [
                    'user' => $user, 'form' => $form
                ]) ?>
            <?php endif ?>

            <h4>Vous avez demandé l'accès au dossier n°<?= $roomModel->id ?></h4>

            <h5>Liste des pièces</h5>

            <div class="register-form">
                <?= $form->field($accessRequest, 'kbis')->widget(FileInput::classname(), $fileInputOptions); ?>
                <?= $form->field($accessRequest, 'balanceSheet')->widget(FileInput::classname(), $fileInputOptions); ?>
                <?= $form->field($accessRequest, 'cni')->widget(FileInput::classname(), $fileInputOptions); ?>

                <?= $form->field($accessRequest, 'presentation')->textArea() ?>

                <div>
                    <h5>Engagement de confidentialité *</h5>

                    <?php if ($roomModel->ca) : ?>
                    <p>Veuillez télécharger <?= Html::a('ce modèle', ['download-ca-document', 'roomID' => $roomModel->id], ['target' => '_blank']) ?>, l'imprimer, <span class="text-red-bold">le signer</span> puis l'uploader ici:</p>
                    <?php endif ?>
                </div>

                <?= $form->field($accessRequest, 'commitment')->widget(FileInput::classname(), $fileInputOptions)->label(false); ?>

                <?php if (!$user->id || $user->isMailingContact): ?>
                    <div id="room-filters">
                        <br>
                        <h4>Champs à remplir pour recevoir nos actualités AJArepreneurs</h4>
                        <?= $form->field($profile, 'targetedSector', ['enableClientValidation' => false])->dropDownList($profile::sectorList(), ['prompt' => '']) ?>
                        <?= $form->field($profile, 'targetedTurnover', ['enableClientValidation' => false])->dropDownList($profile::turnoverList(), ['prompt' => '']) ?>
                        <?= $form->field($profile, 'entranceTicket', ['enableClientValidation' => false])->dropDownList($profile::ticketList(), ['prompt' => '']) ?>

                        <?= $form->field($profile, 'geographicalArea', ['enableClientValidation' => false])->dropDownList($profile::getGeographicalAreaList(), ['prompt' => '']) ?>
                        <?= $form->field($profile, 'targetAmount', ['enableClientValidation' => false])->dropDownList($profile::getTargetAmountList(), ['prompt' => '']) ?>
                        <?= $form->field($profile, 'effective', ['enableClientValidation' => false])->textInput() ?>
                    </div>
                <?php endif ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Ask for access'), ['class' => 'btn btn-block submit-btn']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>