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

                <?= $form->field($accessRequest, 'personType')->dropDownList(\backend\modules\dataroom\models\RoomAccessRequestCoownership::getPersonTypes(), ['prompt' => '']) ?>

                <div class="required">
                    <?= $form->field($accessRequest, 'candidatePresentation')->textarea(['rows' => 5]) ?>

                    <?= $form->field($accessRequest, 'identityCardID')->widget(FileInput::class, $fileInputOptions); ?>
                    <?= $form->field($accessRequest, 'cvID')->widget(FileInput::class, $fileInputOptions); ?>
                    <?= $form->field($accessRequest, 'lastTaxDeclarationID')->widget(FileInput::class, $fileInputOptions); ?>
                    <?= $form->field($accessRequest, 'coownershipManagementReferenceID')->widget(FileInput::class, $fileInputOptions); ?>

                    <?= $form->field($accessRequest, 'groupPresentation')->textarea(['rows' => 5]) ?>
                    <?= $form->field($accessRequest, 'kbisID')->widget(FileInput::class, $fileInputOptions); ?>
                    <?= $form->field($accessRequest, 'latestCertifiedAccountsID')->widget(FileInput::class, $fileInputOptions); ?>
                    <?= $form->field($accessRequest, 'capitalAllocationID')->widget(FileInput::class, $fileInputOptions); ?>
                </div>

                <?php if (!$user->id || $user->isMailingContact): ?>
                    <div id="room-filters">
                        <br>
                        <h4>Champs à remplir pour recevoir nos actualités AJAsyndic</h4>
                        <?= $form->field($profile, 'propertyType', ['enableClientValidation' => false])->dropDownList(\backend\modules\dataroom\models\RoomCoownership::getPropertyTypes(), ['prompt' => '']) ?>

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

                        <?= $form->field($profile, 'lotsNumber', ['enableClientValidation' => false])->textInput() ?>
                        <?= $form->field($profile, 'coownersNumber', ['enableClientValidation' => false])->textInput() ?>
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

        // Bug of Select2 when using client side validation
        setTimeout(function() {
            $('.field-profilecoownership-regionids').removeClass('has-error').find('.help-block').html('');
        }, 200);
    });
    $('#roomaccessrequestcoownership-persontype').trigger('change');
") ?>