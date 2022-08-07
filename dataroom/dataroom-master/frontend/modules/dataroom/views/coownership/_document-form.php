<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\document\models\Document;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $model backend\modules\document\models\Document */
?>

<div class="document-form">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box box-info">

                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false,
                    'options' => ['enctype' => 'multipart/form-data'],

                ]); ?>

                <div class="box-body">
                    <?= $form->field($model, !empty($isMultipleMode) ? 'filePath[]' : 'filePath')->widget(FileInput::classname(), [
                        'options' => [
                            'accept' => 'application/pdf',
                            'multiple' => !empty($isMultipleMode)
                        ],
                        'pluginOptions' => [
                            'dropZoneEnabled' => false,
                            'previewFileType' => 'any',
                            'showUpload' => false,
                            'showPreview' =>  Document::showUploadPreview(),
                            'allowedFileExtensions' => ['pdf'],
                            //'allowedFileExtensions' => ['pdf', 'doc', 'docx', 'txt', 'jpg', 'png', 'gif'],
                            'showCaption' => false,
                            'browseClass' => 'btn btn-primary btn-block',
                            'removeClass' => 'btn btn-default btn-remove',
                            'browseLabel' => (!empty($isMultipleMode) ? Yii::t('admin', 'Choose Documents') : Yii::t('admin', 'Choose Document')) //. '<br>' . Yii::t('admin', 'or') . '<br>' . Yii::t('admin', 'Drag & Drop here')
                        ],
                    ])->label(!empty($isMultipleMode) ? Yii::t('document', 'Files') : Yii::t('document', 'File'))
                        ->hint($model->isNewRecord
                            ? Html::a(!empty($isMultipleMode) ? Yii::t('admin', 'Switch to single mode') : Yii::t('admin', 'Switch to multiple mode'), [!empty($isMultipleMode) ? 'create-document' : 'create-multiple-documents', 'id' => $this->context->detailedRoomModel->id], ['class' => 'btn btn-xs btn-warning pull-right'])
                            : ''); ?>

                    <?php if (empty($isMultipleMode)): ?>
                        <?= $form->field($model, 'title')->textInput(['maxlength' => 70]) ?>
                    <?php endif ?>

                    <?= $form->field($model, 'comment')->textInput(['maxlength' => 250]) ?>

                    <?php if (!$model->isNewRecord && $model->getDocumentPath(true)): ?>
                        <b><?= Html::a(Yii::t('document', 'Download current document'), $model->getDocumentPath(true), ['class' => 'btn btn-warning btn-xs', 'target' => '_blank']) ?></b>
                        <br><br>
                    <?php endif ?>

                    <?= $form->field($model, 'publishDate')->widget(\kartik\datecontrol\DateControl::classname(), [
                        'widgetOptions' => [
                            'removeButton' => false,
                            'options' => ['placeholder' => ''],
                            'pluginOptions' => [
                                'autoclose' => true,
                                'todayHighlight' => true,
                                //'startDate' => '+0d',
                            ]
                        ],
                    ]); ?>
                    <?= Html::a(Yii::t('document', 'Today'), 'javascript:void(0);', ['id' => 'today-date', 'class' => 'btn btn-warning btn-xs']) ?>
                    <?= Html::a(Yii::t('document', 'Void'), 'javascript:void(0);', ['id' => 'empty-date', 'class' => 'btn btn-danger btn-xs']); ?>
                    <br><br>

                    <?= $form->field($model, 'isActive')->checkbox()->hint('Pour que le repreneur voit le document, veuillez vérifier que le document est activé et que la date de publication est correcte.') ?>

                </div>

                <div class="box-footer">
                    <div class="form-group">
                        <?php $label = $model->isNewRecord ? Yii::t('admin', 'Add') : Yii::t('admin', 'Update'); ?>
                        <?= Html::submitButton($label, ['class' => 'btn submit-btn']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

<?php $this->registerJs("
    $('#today-date').click(function() {
        $('#document-publishdate').val('" . date('Y-m-d') . "');
        $('#document-publishdate-disp').val('" . \common\helpers\DateHelper::getFrenchFormatDbDate(date('Y-m-d')) . "');
    });

    $('#empty-date').click(function() {
        $('#document-publishdate').val('');
        $('#document-publishdate-disp').val('');
    });
"); ?>