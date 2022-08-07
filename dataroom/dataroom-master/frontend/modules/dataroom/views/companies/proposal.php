<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;

$this->title = $roomDetails->room->title;

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

<div class="user-proposal container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="content-header col-md-6">
            <div>
                <h1><?= Html::encode($this->title) ?></h1>
            </div>
            <div>
                <a class="btn view-room-btn" href="<?= Url::to(['view-room', 'id' => $roomDetails->id]) ?>">
                    <?= Yii::t('app', 'View room') ?>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="proposal-form">
                <i>tous les champs sont obligatoires</i>
                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false,
                    'options' => ['enctype' => 'multipart/form-data'],
                ]); ?>
                
                <h4>Montant global de l'offre</h4>
                <?= $form->field($proposal, 'tangibleAmount')->textInput(['maxlength' => 50]) ?>
                <?= $form->field($proposal, 'intangibleAmount')->textInput(['maxlength' => 50]) ?>
                <?= $form->field($proposal, 'stock') ?>
                <?= $form->field($proposal, 'workInProgress') ?>

                <h4>Charges augmentatives du prix</h4>
                <?= $form->field($proposal, 'loansRecovery') ?>
                <?= $form->field($proposal, 'paidLeave')->radioList([
                    1 => Yii::t('admin', 'Yes'),
                    0 => Yii::t('admin', 'No'),
                ]) ?>
                <?= $form->field($proposal, 'other')->textArea() ?>
                <?= $form->field($proposal, 'employersNumber')->textInput(['maxlength' => 50]) ?>

                <?= $form->field($proposal, 'documentID')->widget(FileInput::classname(), $fileInputOptions)->hint(Html::a('Télécharger un canevas d\'offre de reprise', ['/dataroom/companies/download-proposal-file'])); ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Make proposal'), ['class' => 'btn btn-block submit-btn']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>