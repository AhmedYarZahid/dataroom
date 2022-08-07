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
                <!--<i>tous les champs sont obligatoires</i>-->
                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false,
                    'options' => ['enctype' => 'multipart/form-data'],
                ]); ?>
                
                <?= $form->field($proposal, 'companyName')->textInput(['maxlength' => 50]) ?>
                <?= $form->field($proposal, 'fullName')->textInput(['maxlength' => 70]) ?>
                <?= $form->field($proposal, 'address')->textInput(['maxlength' => 150]) ?>
                <?= $form->field($proposal, 'phone')->textInput(['maxlength' => 10]) ?>

                <h4>Joindre</h4>
                <?= $form->field($proposal, 'kbisID')->widget(FileInput::class, $fileInputOptions) ?>
                <?= $form->field($proposal, 'cniID')->widget(FileInput::class, $fileInputOptions) ?>
                <?= $form->field($proposal, 'businessCardID')->widget(FileInput::class, $fileInputOptions) ?>

                <br>

                <?= $form->field($proposal, 'documentID')->widget(FileInput::class, $fileInputOptions)
                    ->hint(Html::a('Télécharger un canevas d\'offre de reprise', ['/dataroom/companies/download-proposal-file'])); ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Make proposal'), ['class' => 'btn btn-block submit-btn']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>