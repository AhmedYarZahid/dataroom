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

                <?= $form->field($accessRequest, 'personType')->dropDownList(\backend\modules\dataroom\models\RoomAccessRequestRealEstate::getPersonTypes(), ['prompt' => '']) ?>

                <?= $form->field($accessRequest, 'candidatePresentation', ['options' => ['class' => 'required form-group']])->textarea(['rows' => 5]) ?>

                <?= $form->field($accessRequest, 'identityCardID', ['options' => ['class' => 'required form-group']])->widget(FileInput::class, $fileInputOptions); ?>
                <?= $form->field($accessRequest, 'cvID', ['options' => ['class' => 'required form-group']])->widget(FileInput::class, $fileInputOptions); ?>
                <?= $form->field($accessRequest, 'lastTaxDeclarationID', ['options' => ['class' => 'required form-group']])->widget(FileInput::class, $fileInputOptions); ?>

                <?= $form->field($accessRequest, 'companyPresentation', ['options' => ['class' => 'required form-group']])->textarea(['rows' => 5]) ?>
                <?= $form->field($accessRequest, 'kbisID', ['options' => ['class' => 'required form-group']])->widget(FileInput::class, $fileInputOptions); ?>
                <?= $form->field($accessRequest, 'registrationsUpdatedStatusID', ['options' => ['class' => 'required form-group']])->widget(FileInput::class, $fileInputOptions); ?>
                <?= $form->field($accessRequest, 'latestCertifiedAccountsID', ['options' => ['class' => 'required form-group']])->widget(FileInput::class, $fileInputOptions); ?>
                <?= $form->field($accessRequest, 'capitalAllocationID', ['options' => ['class' => 'required form-group']])->widget(FileInput::class, $fileInputOptions); ?>

                <?php if (!$user->id || $user->isMailingContact): ?>
                    <div id="room-filters">
                        <br>
                        <h4>Champs à remplir pour recevoir nos actualités AJAimmo</h4>
                        <?= $form->field($profile, 'targetSector', ['enableClientValidation' => false])->dropDownList($profile::getTargetSectors(), ['prompt' => '']) ?>

                        <?= $form->field($profile, 'regionIDs', ['enableClientValidation' => false])->widget(\kartik\widgets\Select2::class, [
                            'data' => \common\helpers\ArrayHelper::map(\common\models\Region::find()->all(), 'id', 'nameWithCode'),
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '',
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'tags' => false,
                                'language' => [
                                    'noResults' => new \yii\web\JsExpression('function() {
                                        return "' . Yii::t('admin', 'No regions found.') . '";
                                    }'),
                                ],
                            ],
                            'pluginEvents' => [],
                        ]); ?>

                        <?= $form->field($profile, 'targetedAssetsAmount', ['enableClientValidation' => false])->dropDownList($profile::getTargetedAssetsAmountList(), ['prompt' => '']) ?>
                        <?= $form->field($profile, 'assetsDestination', ['enableClientValidation' => false])->dropDownList($profile::getAssetsDestinationList(), ['prompt' => '']) ?>
                        <?= $form->field($profile, 'operationNature', ['enableClientValidation' => false])->dropDownList($profile::getOperationNatureList(), ['prompt' => '']) ?>
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

<?php $this->registerJs("
    $('body').on('change', '#roomaccessrequestrealestate-persontype', function(event) {
        if ($(this).val() == '" . \backend\modules\dataroom\models\RoomAccessRequestRealEstate::PERSON_TYPE_PHYSICAL . "') {
            $('.field-roomaccessrequestrealestate-identitycardid').show();
            $('.field-roomaccessrequestrealestate-cvid').show();
            $('.field-roomaccessrequestrealestate-lasttaxdeclarationid').show();

            $('.field-roomaccessrequestrealestate-candidatepresentation').hide();
            $('.field-roomaccessrequestrealestate-companypresentation').hide();
            $('.field-roomaccessrequestrealestate-kbisid').hide();
            $('.field-roomaccessrequestrealestate-registrationsupdatedstatusid').hide();
            $('.field-roomaccessrequestrealestate-latestcertifiedaccountsid').hide();
            $('.field-roomaccessrequestrealestate-capitalallocationid').hide();

            // Clear values
            $('#roomaccessrequestrealestate-candidatepresentation').val('');
            $('#roomaccessrequestrealestate-companypresentation').val('');
            $('#roomaccessrequestrealestate-kbisid').fileinput('clear');
            $('#roomaccessrequestrealestate-registrationsupdatedstatusid').fileinput('clear');
            $('#roomaccessrequestrealestate-latestcertifiedaccountsid').fileinput('clear');
            $('#roomaccessrequestrealestate-capitalallocationid').fileinput('clear');
        } else if ($(this).val() == '" . \backend\modules\dataroom\models\RoomAccessRequestRealEstate::PERSON_TYPE_LEGAL . "') {
            $('.field-roomaccessrequestrealestate-identitycardid').hide();
            $('.field-roomaccessrequestrealestate-cvid').hide();
            $('.field-roomaccessrequestrealestate-lasttaxdeclarationid').hide();

            $('.field-roomaccessrequestrealestate-candidatepresentation').show();
            $('.field-roomaccessrequestrealestate-companypresentation').show();
            $('.field-roomaccessrequestrealestate-kbisid').show();
            $('.field-roomaccessrequestrealestate-registrationsupdatedstatusid').show();
            $('.field-roomaccessrequestrealestate-latestcertifiedaccountsid').show();
            $('.field-roomaccessrequestrealestate-capitalallocationid').show();

            // Clear values
            $('#roomaccessrequestrealestate-identitycardid').fileinput('clear');
            $('#roomaccessrequestrealestate-cvid').fileinput('clear');
            $('#roomaccessrequestrealestate-lasttaxdeclarationid').fileinput('clear');
        } else {
            $('.field-roomaccessrequestrealestate-identitycardid').hide();
            $('.field-roomaccessrequestrealestate-cvid').hide();
            $('.field-roomaccessrequestrealestate-lasttaxdeclarationid').hide();

            $('.field-roomaccessrequestrealestate-candidatepresentation').hide();
            $('.field-roomaccessrequestrealestate-companypresentation').hide();
            $('.field-roomaccessrequestrealestate-kbisid').hide();
            $('.field-roomaccessrequestrealestate-registrationsupdatedstatusid').hide();
            $('.field-roomaccessrequestrealestate-latestcertifiedaccountsid').hide();
            $('.field-roomaccessrequestrealestate-capitalallocationid').hide();

            // Clear values
            $('#roomaccessrequestrealestate-identitycardid').fileinput('clear');
            $('#roomaccessrequestrealestate-cvid').fileinput('clear');
            $('#roomaccessrequestrealestate-lasttaxdeclarationid').fileinput('clear');

            $('#roomaccessrequestrealestate-candidatepresentation').val('');
            $('#roomaccessrequestrealestate-companypresentation').val('');
            $('#roomaccessrequestrealestate-kbisid').fileinput('clear');
            $('#roomaccessrequestrealestate-registrationsupdatedstatusid').fileinput('clear');
            $('#roomaccessrequestrealestate-latestcertifiedaccountsid').fileinput('clear');
            $('#roomaccessrequestrealestate-capitalallocationid').fileinput('clear');
        }

        // Bug of Select2 when using client side validation
        setTimeout(function() {
            $('.field-profilerealestate-regionids').removeClass('has-error').find('.help-block').html('');
        }, 200);
    });
    $('#roomaccessrequestrealestate-persontype').trigger('change');
") ?>