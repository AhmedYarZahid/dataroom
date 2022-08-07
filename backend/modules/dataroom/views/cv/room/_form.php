<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use common\helpers\ArrayHelper;

/* @var $model \backend\modules\dataroom\models\RoomCV */
?>

<div class="room-form">
    <div class="row">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin([
                'enableClientValidation' => false,
                'validateOnSubmit' => false,
                'options' => ['enctype' => 'multipart/form-data'],
                //'layout' => 'horizontal',
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => [
                    'labelSpan' => 4
                ]
            ]); ?>

            <?php
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

            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Informations générales</h3>
                </div>

                <div class="box-body">

                    <div class="row">
                        <div class="col-md-10">

                            <?= $this->render('@app/modules/dataroom/views/companies/room/_new_room_begin_form', [
                                'form' => $form,
                                'room' => $room,
                                'model' => $model,
                            ]) ?>

                            <?= $form->field($room, 'title')->textInput(['maxlength' => 255]) ?>

                            <?= $form->field($model, 'ca')->widget(\kartik\widgets\FileInput::classname(), $fileInputOptions)->hint(\common\helpers\FormHelper::downloadLink($model, 'ca')) ?>

                            <?php if ($model->isNewRecord): ?>

                                <?= $form->field($model, 'roomsNumber')->textInput(['type' => 'number', 'min' => 1]) ?>

                            <?php else: ?>

                                <?= $form->field($room, 'mandateNumber', ['options' => ['class' => 'form-group required']])->textInput(['maxlength' => 30]) ?>

                                <?= $form->field($model, 'companyName')->textInput() ?>

                                <?= $form->field($model, 'activityDomainID')->widget(\kartik\widgets\Select2::class, [
                                    'data' => ArrayHelper::map(\backend\modules\dataroom\models\CVActivityDomain::getList(), 'id', 'name'),
                                    'options' => [
                                        'multiple' => false,
                                        'placeholder' => '', //Yii::t('admin', 'Start typing activity domain name'),
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

                                <?= $form->field($model, 'candidateProfile')->textarea(['rows' => 4]) ?>

                                <?= $form->field($model, 'functionID')->widget(\kartik\widgets\Select2::class, [
                                    'data' => ArrayHelper::map(\backend\modules\dataroom\models\CVFunction::getList(), 'id', 'name'),
                                    'options' => [
                                        'multiple' => false,
                                        'placeholder' => '', //Yii::t('admin', 'Start typing function name'),
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
                                        'url' => \yii\helpers\Url::to(['get-cv-function-childs'])
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

                                <?= $form->field($model, 'firstName')->textInput(['maxlength' => 50]) ?>
                                <?= $form->field($model, 'lastName')->textInput(['maxlength' => 50]) ?>
                                <?= $form->field($model, 'address')->textInput(['maxlength' => 150]) ?>
                                <?= $form->field($model, 'email')->textInput(['maxlength' => 150]) ?>
                                <?= $form->field($model, 'phone')->textInput(['maxlength' => 10]) ?>

                                <?= $form->field($model, 'cvID')->widget(\kartik\widgets\FileInput::class, $fileInputOptions)->hint(\common\helpers\FormHelper::downloadLink($model, 'cvID')) ?>

                                <?= $form->field($model, 'departmentID')->widget(\kartik\widgets\Select2::class, [
                                    'data' => ArrayHelper::map(\common\models\Department::getList(), 'id', 'nameWithCode'),
                                    'options' => [
                                        'multiple' => false,
                                        'placeholder' => '', //Yii::t('admin', 'Start typing department name'),
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

                                <?= $form->field($model, 'seniority')->textInput(['maxlength' => 150]) ?>

                                <div class="form-group">
                                    <label class="control-label col-md-4">URL pour accès à la room</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" value="<?= $model->url ?>" disabled="">
                                    </div>
                                    <div class="col-md-1">
                                        <a href="<?= $model->url ?>" target="_blank" style="line-height: 34px">Ouvrir</a>
                                    </div>
                                </div>

                            <?php endif ?>

                        </div>
                    </div>

                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <?php $label = $model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Update'); ?>
                    <?= Html::submitButton($label, ['class' => 'btn btn-lg btn-primary btn-block']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>