<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\document\models\Document;
use kartik\widgets\FileInput;

?>

<div class="document-form">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">

                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false,
                    'options' => ['enctype' => 'multipart/form-data'],

                ]); ?>

                <div class="box-body">
                    <?= $form->field($model, 'title')->textInput(['maxlength' => 70]) ?>

                    <?= $form->field($model, 'comment')->textInput(['maxlength' => 250]) ?>

                    <?= $form->field($model, 'filePath[]')->widget(FileInput::classname(), [
                        'options' => [
                            'accept' => 'application/pdf',
                            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'previewFileType' => 'any',
                            'showUpload' => false,
                            'allowedFileExtensions' => ['pdf', 'doc', 'docx', 'txt', 'jpg', 'png', 'gif'],
                            'showCaption' => false,
                            'browseClass' => 'btn btn-primary btn-block',
                            'removeClass' => 'btn btn-default btn-remove',
                        ],
                    ])->hint(Yii::t('admin', 'If you chosen multiple documents - only one archive will be created with uploaded files.'))
                        ->label(Yii::t('document', 'Files')); ?>
                    <?php if (!$model->isNewRecord && $model->getDocumentPath(true)): ?>
                        <b><?= Html::a(Yii::t('document', 'Download current document'), $model->getDocumentUrl(true), ['class' => 'btn btn-info btn-xs', 'target' => '_blank']) ?></b>
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

                    <?= $form->field($model, 'rank')->textInput() ?>

                    <?= $form->field($model, 'isActive')->checkbox() ?>

                </div>

                <div class="box-footer">
                    <div class="form-group">
                        <?php $label = $model->isNewRecord ? Yii::t('admin', 'Add') : Yii::t('admin', 'Update'); ?>
                        <?= Html::submitButton($label, ['class' => 'btn btn-block btn-lg btn-primary']) ?>
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