<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\datecontrol\DateControl;
use kartik\widgets\FileInput;
use frontend\assets\SmartPhotoAsset;
use backend\modules\dataroom\models\RoomCoownership;
use common\helpers\ArrayHelper;

/* @var $room \backend\modules\dataroom\models\Room */
/* @var $model \backend\modules\dataroom\models\RoomCV */

$this->title = $model->room->title;
$disabledMode = !empty($disabledMode) || $room->status != \backend\modules\dataroom\models\Room::STATUS_DRAFT;

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
        'allowedFileExtensions' => ['pdf'],
        'browseClass' => 'btn btn-primary btn-block',
        'removeClass' => 'btn btn-default btn-remove',
    ],
];
?>

<div class="update-room container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php
            $titlePrefix = '';
            if ($model->room->isExpired()) {
                $titlePrefix = '[' . $model->room->statusLabel() . '] ';
            } ?>

            <h1><?= Html::encode($titlePrefix . $this->title) ?></h1>

            <?php if ($disabledMode): ?>
                <div><?= Yii::t('app', 'The submission of offers is done electronically and by paper.') ?></div>
            <?php endif ?>

            <?php if (!$disabledMode && $model->state == \backend\modules\dataroom\models\RoomCV::STATE_READY): ?>

                <?php if ($model->room->status == \backend\modules\dataroom\models\Room::STATUS_DRAFT): ?>

                    <div>
                        Votre CV est en cours d’étude par AJAssociés et sera en ligne dans 72h.
                        <br>
                        Pour annuler, la mise en ligne de votre CV, veuillez cliquer sur le bouton "Désactiver".
                    </div>

                <?php else: ?>

                    <!--<div>
                        Votre CV est en ligne.
                        <br>
                        Pour annuler, la mise en ligne de votre CV, veuillez cliquer sur le bouton "Désactiver".
                        <br>
                        A chaque fois que vous désactiverez votre CV puis que vous le mettrez à jour en cliquant sur le bouton en-bas de page, le CV sera de nouveau en cours d'étude par AJAssociés puis sera mis en ligne dans 72h.
                    </div>-->

                <?php endif ?>

                <br>

                <?= Html::a(Yii::t('admin', 'Deactivate'), ['deactivate', 'id' => $model->id], [
                    'class' => 'btn btn-xs btn-warning',
                    'data-confirm' => Yii::t('admin', 'Are you sure you want to deactivate this room?')
                ]) ?>
            <?php elseif (!$disabledMode && $model->state != \backend\modules\dataroom\models\RoomCV::STATE_READY): ?>
                <div>
                    Votre CV est hors-ligne/à corriger.
                    <br>
                    ll sera ensuite en cours d'étude par AJAssociés puis sera mis en ligne dans 72h.
                    <br>
                    A chaque fois que vous désactiverez votre CV puis que vous le mettrez à jour en cliquant sur le bouton en-bas de page, le CV sera de nouveau en cours d'étude par AJAssociés puis mis en ligne dans 72h.
                </div>
            <?php endif ?>

            <?php if ($disabledMode && $room->isExpired() && $room->proposalsAllowed) : ?>
                <div>L’offre étant expirée, vous pouvez effectuer une offre de reprise, cependant nous ne garantissons pas de pouvoir la soumettre.</div>
            <?php endif ?>
        </div>
    </div>

    <div class="row">
        <div class="room-form col-md-6 col-md-offset-3">
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

                    <?= $form->field($room, 'title')->textInput(['maxlength' => 255, 'disabled' => $disabledMode]) ?>

                    <?= $form->field($model, 'companyName')->textInput(['disabled' => $disabledMode]) ?>

                    <?= $form->field($model, 'activityDomainID')->widget(\kartik\widgets\Select2::class, [
                        'data' => ArrayHelper::map(\backend\modules\dataroom\models\CVActivityDomain::getList(), 'id', 'name'),
                        'options' => [
                            'multiple' => false,
                            'placeholder' => '', //Yii::t('admin', 'Start typing activity domain name'),
                            'disabled' => $disabledMode
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'tags' => false,
                            'language' => [
                                'noResults' => new \yii\web\JsExpression('function() {
                                        return "' . Yii::t('admin', 'No activity domains found.') . '";
                                    }'),
                            ],
                        ],
                        'pluginEvents' => [],
                    ]); ?>

                    <?= $form->field($model, 'candidateProfile')->textarea(['rows' => 4,  'disabled' => $disabledMode]) ?>

                    <?= $form->field($model, 'functionID')->widget(\kartik\widgets\Select2::class, [
                        'data' => ArrayHelper::map(\backend\modules\dataroom\models\CVFunction::getList(), 'id', 'name'),
                        'options' => [
                            'multiple' => false,
                            'placeholder' => '', //Yii::t('admin', 'Start typing function name'),
                            'disabled' => $disabledMode
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'tags' => false,
                            'language' => [
                                'noResults' => new \yii\web\JsExpression('function() {
                                        return "' . Yii::t('admin', 'No functions found.') . '";
                                    }'),
                            ],
                        ],
                        'pluginEvents' => [],
                    ]); ?>

                    <?= $form->field($model, 'subFunctionID')->widget(\kartik\depdrop\DepDrop::class, [
                        'type' => \kartik\widgets\DepDrop::TYPE_SELECT2,
                        'data' => $model->functionID
                            ? ArrayHelper::map(\backend\modules\dataroom\models\CVFunction::getList($model->functionID), 'id', 'name')
                            : [],
                        'pluginOptions' => [
                            'placeholder' => '', //Yii::t('app', 'Start typing Sub-Function name'),
                            'depends' => [Html::getInputId($model, 'functionID')],
                            'url' => \yii\helpers\Url::to(['get-cv-function-childs']),
                            'disabled' => $disabledMode
                            //'initialize' => true,
                        ],
                        'pluginEvents' => [
                            "change" => "function(event, id, value, count) {}",
                        ],
                        'select2Options' => [
                            'pluginOptions' => [
                                'allowClear' => true,
                                'tags' => false,
                                'language' => [
                                    'noResults' => new \yii\web\JsExpression('function() {
                                           return "' . Yii::t('app', 'No results found.') . '";
                                        }')
                                ]
                            ],
                        ]
                    ]); ?>

                    <?= $form->field($model, 'firstName')->textInput(['maxlength' => 50, 'disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'lastName')->textInput(['maxlength' => 50, 'disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'address')->textInput(['maxlength' => 150, 'disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'email')->textInput(['maxlength' => 150, 'disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'phone')->textInput(['maxlength' => 10, 'disabled' => $disabledMode]) ?>

                    <?php if (!$disabledMode) : ?>
                        <?= $form->field($model, 'cvID')->widget(\kartik\widgets\FileInput::class, $fileInputOptions)->hint(\common\helpers\FormHelper::downloadLink($model, 'cvID')) ?>
                    <?php else : ?>
                        <div class="form-group">
                            <label class="control-label">
                                <?= $model->getAttributeLabel('cvID') ?> :
                            </label>
                            &nbsp;&nbsp;<?= \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->cv->getDocumentUrl(), ['target' => '_blank']) ?>
                        </div>
                    <?php endif ?>

                    <?= $form->field($model, 'departmentID')->widget(\kartik\widgets\Select2::class, [
                        'data' => ArrayHelper::map(\common\models\Department::getList(), 'id', 'nameWithCode'),
                        'options' => [
                            'multiple' => false,
                            'placeholder' => '', //Yii::t('admin', 'Start typing department name'),
                            'disabled' => $disabledMode
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'tags' => false,
                            'language' => [
                                'noResults' => new \yii\web\JsExpression('function() {
                                        return "' . Yii::t('admin', 'No departments found.') . '";
                                    }'),
                            ],
                        ],
                        'pluginEvents' => [],
                    ]); ?>

                    <?php if (!$disabledMode) : ?>
                        <?= $form->field($model, 'regionID')->widget(\kartik\depdrop\DepDrop::class, [
                            'type' => \kartik\widgets\DepDrop::TYPE_DEFAULT,
                            'data' => [$model->regionID => $model->getRegionName()], // ensure at least the preselected value is available
                            'pluginOptions' => [
                                'placeholder' => '',
                                'depends' => [Html::getInputId($model, 'departmentID')],
                                'url' => \yii\helpers\Url::to(['get-region-by-department']),
                            ],
                            'readonly' => true,
                            'pluginEvents' => [
                                "change" => "function(event, id, value, count) {}",
                            ],
                        ])->hint(Yii::t('admin', 'This field will be automatically filled depending on department chosen.')); ?>
                    <?php else: ?>
                        <?= $form->field($model, 'regionID')->textInput(['value' => $model->getRegionName(), 'disabled' => true]) ?>
                    <?php endif ?>

                    <?= $form->field($model, 'seniority')->textInput(['maxlength' => 150, 'disabled' => $disabledMode]) ?>
                </div>
            </div>

            <div class="form-group">
                <?php if (!$disabledMode) : ?>
                    <?= Html::submitButton(Yii::t('app', 'Update room'), ['class' => 'btn btn-block submit-btn']) ?>
                <?php endif ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>