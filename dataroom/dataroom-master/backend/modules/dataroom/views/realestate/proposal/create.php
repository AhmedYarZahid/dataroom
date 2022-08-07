<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\FileInput;

$this->title = Yii::t('admin', 'Add proposal');

$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-table"></i> AJAimmo', 'url' => ['realestate/room/index']];
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-book"></i> ' . Yii::t('admin', 'Proposals'), 'url' => ['realestate/room/proposals', 'id' => $room->detailedRoom->id]];
$this->params['breadcrumbs'][] = $this->title;
//Offres des repreneurs

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

<div class="room-form">
    <h1><?= Html::encode($this->title) ?></h1>


    <?php $form = ActiveForm::begin([
        'enableClientValidation' => false,
        'validateOnSubmit' => false,
        'options' => ['enctype' => 'multipart/form-data'],
        'layout' => 'horizontal',
    ]); ?>

    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title">Informations utilisateur</h3>
        </div>
        <div class="box-body">
            <?= $form->field($proposal, 'userID')->widget(Select2::classname(), [
                'data' => $proposal->getUserList($room->id),
                'pluginOptions'=> [
                    'allowClear' => true,
                    'placeholder' => 'Choisissez un utilisateur',
                    //'showToggleAll' => false,
                ],
            ])->label("Email de l'utilisateur")->hint('Les utilisateurs qui ont déjà fait une offre de reprise pour cette room sont exclus de la liste.') ?>
        </div>
    </div>

    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title">Informations sur la proposition</h3>
        </div>
        <div class="box-body">
            <?= $form->field($proposal, 'documentID')->widget(FileInput::class, $fileInputOptions)
                ->hint(Html::a('Télécharger un modèle', ['/dataroom/companies/proposal/download-template'])); ?>

            <?= $form->field($proposal, 'firstName')->textInput(['maxlength' => 50]) ?>
            <?= $form->field($proposal, 'lastName')->textInput(['maxlength' => 70]) ?>
            <?= $form->field($proposal, 'address')->textInput(['maxlength' => 150]) ?>
            <?= $form->field($proposal, 'phone')->textInput(['maxlength' => 10]) ?>

            <h4>Joindre</h4>
            <?= $form->field($proposal, 'kbisID')->widget(FileInput::class, $fileInputOptions) ?>
            <?= $form->field($proposal, 'cniID')->widget(FileInput::class, $fileInputOptions) ?>
            <?= $form->field($proposal, 'balanceSheetID')->widget(FileInput::class, $fileInputOptions) ?>
            <?= $form->field($proposal, 'taxNoticeID')->widget(FileInput::class, $fileInputOptions) ?>
        </div>
        <div class="box-footer">
            <div class="form-group">
                <div class="col-sm-12">
                    <?= Html::submitButton(Yii::t('admin', 'Create'), ['class' => 'btn btn-block btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>