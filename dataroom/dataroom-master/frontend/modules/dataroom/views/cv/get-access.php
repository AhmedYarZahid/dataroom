<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\form\ActiveForm;
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
                /*'enableAjaxValidation' => false,
                'enableClientValidation' => true,

                'validateOnChange' => false,
                'validateOnBlur' => false,
                'validateOnSubmit' => true,*/

                'options' => ['enctype' => 'multipart/form-data'],
            ]); ?>

            <?php if (!$user->id) : ?>
                <?= $this->render('/user/_register_form', [
                    'user' => $user, 'form' => $form
                ]) ?>
            <?php endif ?>

            <br>
            <h4>Vous avez demandé l'accès au dossier n°<?= $roomModel->id ?></h4>

            <h5>Liste des pièces</h5>

            <div class="register-form">

                <div>
                    <h5>Engagement de confidentialité *</h5>

                    <?php if ($roomModel->ca) : ?>
                        <p>Veuillez télécharger <?= Html::a('ce modèle', ['download-ca-document', 'roomID' => $roomModel->id], ['target' => '_blank']) ?>, l'imprimer, <span class="text-red-bold">le signer</span> puis l'uploader ici:</p>
                    <?php endif ?>
                </div>

                <?= $form->field($accessRequest, 'agreementID')->widget(FileInput::classname(), $fileInputOptions)->label(false); ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Ask for access'), ['class' => 'btn btn-block submit-btn']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php $this->registerJs("
    $('body').on('change', '#roomaccessrequestcoownership-persontype', function(event) {
        if ($(this).val() == '" . \backend\modules\dataroom\models\RoomAccessRequestCoownership::PERSON_TYPE_PHYSICAL . "') {
            $('.field-roomaccessrequestcoownership-identitycardid').show();
            $('.field-roomaccessrequestcoownership-cvid').show();
            $('.field-roomaccessrequestcoownership-lasttaxdeclarationid').show();
            $('.field-roomaccessrequestcoownership-coownershipmanagementreferenceid').show();

            $('.field-roomaccessrequestcoownership-candidatepresentation').hide();
            $('.field-roomaccessrequestcoownership-grouppresentation').hide();
            $('.field-roomaccessrequestcoownership-kbisid').hide();
            $('.field-roomaccessrequestcoownership-latestcertifiedaccountsid').hide();
            $('.field-roomaccessrequestcoownership-capitalallocationid').hide();

            // Clear values
            $('#roomaccessrequestcoownership-candidatepresentation').val('');
            $('#roomaccessrequestcoownership-grouppresentation').val('');

            $('#roomaccessrequestcoownership-kbisid').fileinput('clear');
            $('#roomaccessrequestcoownership-latestcertifiedaccountsid').fileinput('clear');
            $('#roomaccessrequestcoownership-capitalallocationid').fileinput('clear');
        } else if ($(this).val() == '" . \backend\modules\dataroom\models\RoomAccessRequestCoownership::PERSON_TYPE_LEGAL . "') {
            $('.field-roomaccessrequestcoownership-identitycardid').hide();
            $('.field-roomaccessrequestcoownership-cvid').hide();
            $('.field-roomaccessrequestcoownership-lasttaxdeclarationid').hide();
            $('.field-roomaccessrequestcoownership-coownershipmanagementreferenceid').hide();

            $('.field-roomaccessrequestcoownership-candidatepresentation').show();
            $('.field-roomaccessrequestcoownership-grouppresentation').show();
            $('.field-roomaccessrequestcoownership-kbisid').show();
            $('.field-roomaccessrequestcoownership-latestcertifiedaccountsid').show();
            $('.field-roomaccessrequestcoownership-capitalallocationid').show();

            // Clear values
            $('#roomaccessrequestcoownership-identitycardid').fileinput('clear');
            $('#roomaccessrequestcoownership-cvid').fileinput('clear');
            $('#roomaccessrequestcoownership-lasttaxdeclarationid').fileinput('clear');
            $('#roomaccessrequestcoownership-coownershipmanagementreferenceid').fileinput('clear');
        } else {
            $('.field-roomaccessrequestcoownership-identitycardid').hide();
            $('.field-roomaccessrequestcoownership-cvid').hide();
            $('.field-roomaccessrequestcoownership-lasttaxdeclarationid').hide();

            $('.field-roomaccessrequestcoownership-candidatepresentation').hide();
            $('.field-roomaccessrequestcoownership-grouppresentation').hide();
            $('.field-roomaccessrequestcoownership-kbisid').hide();
            $('.field-roomaccessrequestcoownership-coownershipmanagementreferenceid').hide();
            $('.field-roomaccessrequestcoownership-latestcertifiedaccountsid').hide();
            $('.field-roomaccessrequestcoownership-capitalallocationid').hide();

            // Clear values
            $('#roomaccessrequestcoownership-identitycardid').fileinput('clear');
            $('#roomaccessrequestcoownership-cvid').fileinput('clear');
            $('#roomaccessrequestcoownership-lasttaxdeclarationid').fileinput('clear');

            $('#roomaccessrequestcoownership-candidatepresentation').val('');
            $('#roomaccessrequestcoownership-grouppresentation').val('');
            $('#roomaccessrequestcoownership-kbisid').fileinput('clear');
            $('#roomaccessrequestcoownership-coownershipmanagementreferenceid').fileinput('clear');
            $('#roomaccessrequestcoownership-latestcertifiedaccountsid').fileinput('clear');
            $('#roomaccessrequestcoownership-capitalallocationid').fileinput('clear');
        }
    });
    $('#roomaccessrequestcoownership-persontype').trigger('change');
") ?>