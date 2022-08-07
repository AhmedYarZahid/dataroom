<?php

use common\helpers\FormHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\datecontrol\DateControl;
use kartik\widgets\Select2;
use kartik\widgets\FileInput;
use frontend\assets\SmartPhotoAsset;

$this->title = $model->room->title;

$isAdmin = Yii::$app->user->identity->isAdmin();
$disabledMode = !empty($disabledMode);

$fileInputOptions = [
    'options' => [
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

$dateControlOptions = [
    'displayFormat' => 'php:d/m/Y',
    'saveFormat' => 'php:Y-m-d',
    'widgetOptions' => [
        'removeButton' => false,
        'options' => ['placeholder' => '', 'disabled' => $disabledMode],
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
            //'startDate' => '+0d',
        ]
    ],
];

$procedureDateControlOptions = [
    'displayFormat' => 'php:d/m/Y',
    'saveFormat' => 'php:Y-m-d H:i:s',
    'widgetOptions' => [
        'removeButton' => false,
        'options' => ['placeholder' => '', 'disabled' => $disabledMode],
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
            //'startDate' => '+0d',
        ]
    ],
];

$expirationDateOptions = array_merge($dateControlOptions, [
    'type' => DateControl::FORMAT_DATETIME,
    'displayFormat' => 'php:d/m/Y H:i',
]);

$historyLabels = [
    'Année N', 'Année N-1', 'Année N-2', 'Prévisionnel Année N+1',
];

$initialPreview = [];
$initialPreviewConfig = [];

if ($room->images) {
    foreach ($room->images as $document) {
        $initialPreview[] = $document->getDocumentUrl();
        $initialPreviewConfig[] = [
            'caption' => $document->getDocumentName(),
            'size' => $document->size,
            'url' => Url::to(['delete-images']),
            'key' => $document->id,
        ];
    }
}

SmartPhotoAsset::register($this);
?>

<div class="update-room container">
    <div class="row">
        <div class="col-md-12 text-center">

            <?php
            $titlePrefix = '';
            if ($model->room->isExpired()) {
                $titlePrefix = '[' . $model->room->statusLabel() . '] ';
            } ?>

            <h1><?= Html::encode($titlePrefix . $this->title) ?></h1>

            <?php if ($disabledMode): ?>
                <div><?= Yii::t('app', 'The submission of offers is done electronically and by paper.') ?></div>
            <?php endif ?>

            <?php if ($disabledMode && $room->isExpired() && $room->proposalsAllowed): ?>
                <div>L’offre étant expirée, vous pouvez effectuer une offre de reprise, cependant nous ne garantissons pas de pouvoir la soumettre.</div>
            <?php endif ?>    
        </div>
    </div>

    <div class="row">
        <div class="room-form col-md-12">
            <?php $form = ActiveForm::begin([
                'enableClientValidation' => false,
                'validateOnSubmit' => false,
                'options' => ['enctype' => 'multipart/form-data'],
            ]); ?>

            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Informations générales
                        <div class="pull-right">
                            <a class="btn btn-default btn-sm" data-toggle="collapse" data-target="#general-info" role="button" aria-expanded="true" aria-controls="general-info">V</a>
                        </div>
                    </h3>
                </div>

                <div class="box-body collapse in" id="general-info">
                    <?= $form->field($model, 'id')->textInput(['disabled' => true]) ?>
                    <?= $form->field($room, 'title')->textInput(['maxlength' => 255, 'disabled' => !$isAdmin]) ?>
                    <?= $form->field($model, 'activity')->textInput(['maxlength' => 255, 'disabled' => !$isAdmin]) ?>
                    <?= $form->field($model, 'region')->textInput(['maxlength' => 255, 'disabled' => !$isAdmin]) ?>
                    <?= $form->field($model, 'website')->textInput(['maxlength' => 255, 'disabled' => !$isAdmin]) ?>
                    <?= $form->field($model, 'address')->textInput(['maxlength' => 255, 'disabled' => !$isAdmin]) ?>
                    <?= $form->field($model, 'zip')->textInput(['maxlength' => 5, 'disabled' => !$isAdmin]) ?>
                    <?= $form->field($model, 'city')->textInput(['maxlength' => 255, 'disabled' => !$isAdmin]) ?>
                    <?= $form->field($model, 'desc')->textArea(['disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'desc2')->textArea(['disabled' => $disabledMode]) ?>

                    <?php if ($isAdmin) : ?>
                        <?= $form->field($model, 'ca')->widget(FileInput::classname(), $fileInputOptions)->hint(Html::a(Yii::t('admin', 'Download'), ['download-ca-document', 'roomID' => $model->id], ['target' => '_blank'])) ?>
                    <?php else : ?>
                        <div class="form-group">
                            <label class="control-label">
                                Engagement de confidentialité :
                            </label>
                            &nbsp;&nbsp;<?= Html::a(Yii::t('admin', 'Download'), ['download-ca-document', 'roomID' => $model->id], ['target' => '_blank']) ?>
                        </div>
                    <?php endif ?>

                </div>
            </div>

            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Images
                        <div class="pull-right">
                            <a class="btn btn-default btn-sm" data-toggle="collapse" data-target="#images-info" role="button" aria-expanded="true" aria-controls="images-info">V</a>
                        </div>
                    </h3>
                </div>

                <div class="box-body collapse in" id="images-info">

                    <?php if (!$disabledMode): ?>

                        <?= $form->field($room, 'imageFiles', [
                            'horizontalCssClasses' => ['wrapper' => 'col-sm-12'],
                        ])->widget(FileInput::class, [
                            'options' => [
                                'multiple' => true,
                                'accept' => 'image/*',
                            ],
                            'pluginOptions' => [
                                'previewFileType' => 'any',
                                'showPreview' => true,
                                'showCaption' => false,
                                'showRemove' => true,
                                'showUpload' => true,
                                'showClose' => false,
                                'fileActionSettings' => ['showDrag' => false],
                                'allowedFileExtensions' => ['jpg','jpeg','gif','png'],
                                'browseClass' => 'btn btn-primary btn-block',
                                'uploadUrl' => Url::to(['upload-images', 'id' => $model->id]),
                                'deleteUrl' => Url::to(['delete-images']),
                                'initialPreview' => $initialPreview,
                                'initialPreviewConfig' => $initialPreviewConfig,
                                'overwriteInitial' => false,
                                'initialPreviewAsData' => true,
                            ],
                            'pluginEvents' => [
                                'filebatchselected' => "function(event, files) { $(this).fileinput('upload') }",
                            ],
                        ])->label('') ?>

                    <?php elseif ($room->images) : ?>

                        <div class="image-gallery">
                            <?php foreach ($room->images as $image) : ?>
                                <a class="image-gallery-item" href="<?= $image->getDocumentUrl() ?>" data-group="room-images">
                                    <?= Html::img($image->getDocumentUrl()) ?>
                                </a>
                            <?php endforeach ?>
                        </div>
                    <?php else: ?>
                        - Pas d'images -
                    <?php endif ?>
                </div>
            </div>

            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Administratif et financier
                        <div class="pull-right">
                            <a class="btn btn-default btn-sm" data-toggle="collapse" data-target="#financial-info" role="button" aria-expanded="true" aria-controls="financial-info">V</a>
                        </div>
                    </h3>
                </div>

                <div class="box-body collapse in" id="financial-info">

                    <?= $form->field($model, 'siren')->textInput(['maxlength' => 9, 'disabled' => !$isAdmin]) ?>

                    <?php if ($isAdmin) : ?>
                        <?= $form->field($model, 'codeNaf')->widget(Select2::class, [
                            'data' => $model->codeNafList(),
                            'pluginOptions'=> [
                                'allowClear' => true,
                                'placeholder' => '',
                                'disabled' => !$isAdmin,
                            ],
                        ]) ?>
                    <?php else : ?>
                        <?= $form->field($model, 'codeNaf')->textInput(['disabled' => true]) ?>
                    <?php endif ?>

                    <?= $form->field($model, 'legalStatus')->textInput(['maxlength' => 255, 'disabled' => !$isAdmin]) ?>

                    <table class="table history">
                        <thead>
                        <tr>
                            <th width="180px"></th>
                            <th>Date de début</th>
                            <th>Date de fin</th>
                            <th>Nombre de mois</th>
                            <th>Chiffres d'affaires</th>
                            <th>Marge brute</th>
                            <th>Résultat d'exploitation</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php for ($i = 0; $i < 4; $i++) : ?>
                            <tr>
                                <td><?= $historyLabels[$i] ?></td>
                                <td><?= $form->field($model, "history[$i][start_date]")->widget(DateControl::class, $dateControlOptions)->label(false) ?></td>
                                <td><?= $form->field($model, "history[$i][end_date]")->widget(DateControl::class, $dateControlOptions)->label(false) ?></td>
                                <td><?= $form->field($model, "history[$i][months]")->textInput(['maxlength' => 255, 'disabled' => $disabledMode])->label(false) ?></td>
                                <td><?= $form->field($model, "history[$i][sales]")->textInput(['maxlength' => 255, 'disabled' => $disabledMode])->label(false) ?></td>
                                <td><?= $form->field($model, "history[$i][margin]")->textInput(['maxlength' => 255, 'disabled' => $disabledMode])->label(false) ?></td>
                                <td><?= $form->field($model, "history[$i][profit]")->textInput(['maxlength' => 255, 'disabled' => $disabledMode])->label(false) ?></td>
                            </tr>
                        <?php endfor ?>
                        </tbody>
                    </table>

                    <?= $form->field($model, 'concurrence')->textArea(['disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'annualTurnover')->textInput(['maxlength' => 50, 'disabled' => $disabledMode]) ?>
                </div>
            </div>


            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Social
                        <div class="pull-right">
                            <a class="btn btn-default btn-sm" data-toggle="collapse" data-target="#social-info" role="button" aria-expanded="true" aria-controls="social-info">V</a>
                        </div>
                    </h3>
                </div>

                <div class="box-body collapse in" id="social-info">
                    <?= $form->field($model, 'contributors')->textInput(['maxlength' => 50, 'disabled' => $disabledMode]) ?>
                </div>
            </div>

            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Procédure
                        <div class="pull-right">
                            <a class="btn btn-default btn-sm" data-toggle="collapse" data-target="#procedure-info" role="button" aria-expanded="true" aria-controls="procedure-info">V</a>
                        </div>
                    </h3>
                </div>

                <div class="box-body collapse in" id="procedure-info">
                    <?= $form->field($model, 'procedureNature')->textInput(['maxlength' => 255, 'disabled' => !$isAdmin]) ?>

                    <?php if ($isAdmin) : ?>
                        <?= $form->field($model, 'designationDate')->widget(DateControl::class, $procedureDateControlOptions) ?>
                    <?php else : ?>
                        <?= $form->field($model, 'designationDate')->textInput(['disabled' => true, 'value' => $model->designationDate ? $model->designationDate->format('d/m/Y') : null]) ?>
                    <?php endif ?>

                    <?= $form->field($model, 'procedureContact')->textArea(['disabled' => !$isAdmin]) ?>
                    <?= $form->field($model, 'companyContact')->textInput(['maxlength' => 255, 'disabled' => $disabledMode]) ?>

                    <?php if ($isAdmin) : ?>
                        <?= $form->field($room, 'publicationDate')->widget(DateControl::class, $procedureDateControlOptions) ?>
                        <?= $form->field($room, 'archivationDate')->widget(DateControl::class, $procedureDateControlOptions) ?>
                        <?= $form->field($model, 'hearingDate')->widget(DateControl::class, $procedureDateControlOptions) ?>
                        <?= $form->field($model, 'refNumber0')->widget(DateControl::class, $expirationDateOptions) ?>
                        <?= $form->field($model, 'refNumber1')->widget(DateControl::class, $expirationDateOptions) ?>
                        <?= $form->field($model, 'refNumber2')->widget(DateControl::class, $expirationDateOptions) ?>
                        <?= $form->field($room, 'createdDate')->widget(DateControl::class, $procedureDateControlOptions) ?>
                    <?php else : ?>
                        <?= $form->field($room, 'publicationDate')->textInput(['disabled' => true, 'value' => $room->publicationDate->format('d/m/Y')]) ?>
                        <?= $form->field($room, 'archivationDate')->textInput(['disabled' => true, 'value' => $room->archivationDate->format('d/m/Y')]) ?>
                        <?= $form->field($model, 'hearingDate')->textInput(['disabled' => true, 'value' => $model->hearingDate ? $model->hearingDate->format('d/m/Y') : null]) ?>
                        <?= $form->field($model, 'refNumber0')->textInput(['disabled' => true, 'value' => $model->refNumber0 ? $model->refNumber0->format('d/m/Y H:i') : null]) ?>
                        <?= $form->field($model, 'refNumber1')->textInput(['disabled' => true, 'value' => $model->refNumber1 ? $model->refNumber1->format('d/m/Y H:i') : null]) ?>
                        <?= $form->field($model, 'refNumber2')->textInput(['disabled' => true, 'value' => $model->refNumber2 ? $model->refNumber2->format('d/m/Y H:i') : null]) ?>
                        <?= $form->field($room, 'createdDate')->textInput(['disabled' => true, 'value' => $room->createdDate->format('d/m/Y')]) ?>
                    <?php endif ?>

                    <div class="form-group">
                        <img src="<?= $model->qrCodeSrc ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?php if (!$disabledMode) : ?>
                    <?= Html::submitButton(Yii::t('app', 'Update room'), ['class' => 'btn btn-block submit-btn']) ?>
                <?php elseif (Yii::$app->user->can('makeProposal', ['room' => $room])) : ?>
                    <a class="btn btn-block submit-btn" href="<?= Url::to(['proposal', 'id' => $model->id]) ?>">
                        Faire une proposition
                    </a>
                <?php elseif ($user->hasProposal($room)) : ?>
                    <a class="btn btn-block submit-btn disabled">Proposition envoyée</a>
                <?php endif ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php $this->registerJs("
    new SmartPhoto('.image-gallery-item');
"); ?>