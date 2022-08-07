<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\contact\models\Contact;
use backend\modules\contact\models\ContactTemplate;
use kartik\popover\PopoverX;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model backend\modules\contact\models\Contact */
/* @var $answerModel backend\modules\contact\models\ContactThread */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('admin', 'Contact:') . ' ' . $model->subject;

$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-comments-o"></i> ' . Yii::t('contact', 'Contact'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->subject];
?>

    <div class="contact-view">

        <!--<h1><? /*= Html::encode($this->title) */ ?></h1>-->

        <?php echo $this->render('_contact-preview', ['model' => $model]); ?>

        <?php if ($model->isClosed): ?>
            <h4>
                <div class="pull-left"><span class="glyphicon glyphicon-lock" style="color: maroon;"></span></div>
                &nbsp;
                <?php echo Yii::t('contact', "This topic is closed. Please open it to be able to send a new reply."); ?>
            </h4>
            <br>
        <?php else: ?>
            <br>
            <div class="contact-form">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-success">

                            <?php $form = ActiveForm::begin([
                                'enableClientValidation' => false,
                                'validateOnSubmit' => false,
                                'options' => ['enctype' => 'multipart/form-data'],

                            ]); ?>

                            <div class="box-header">
                                <h3 class="box-title"><?php echo Yii::t('contact', 'New reply'); ?></h3>
                            </div>

                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-7">
                                        <?= $form->field($answerModel, 'body')->widget(common\widgets\imperavi\Widget::classname(), [
                                            'id' => 'contactthread-body',
                                            'settings' => [
                                                //'buttons' => "js:['formatting', '|', 'bold', 'italic', 'deleted', '|', 'fontcolor', 'alignment', 'horizontalrule']",
                                                //'lang' => 'fr',
                                                'buttonSource' => true,
                                                'minHeight' => 200,
                                                'linebreaks' => false,
                                                'replaceDivs' => false,
                                                'imageUpload' => \yii\helpers\Url::to(['upload-image']),
                                                'imageUploadFields' => [
                                                    Yii::$app->request->csrfParam => Yii::$app->request->getCsrfToken()
                                                ],
                                                'imageManagerJson' => \yii\helpers\Url::to(['get-images']),
                                                'changeCallback' => new yii\web\JsExpression('function() { updateSavePossibility(); }'),

                                                'plugins' => [
                                                    'source',
                                                    'imagemanager',
                                                    'table',
                                                    'fontcolor',
                                                    'fullscreen',
                                                ]
                                            ],
                                        ])->label(''); ?>

                                        <?php PopoverX::begin([
                                            'id' => 'new-template-popover',
                                            'header' => Yii::t('contact', 'Save as a new template'),
                                            'placement' => PopoverX::ALIGN_TOP_LEFT,
                                            'footer' => Html::button(Yii::t('contact', 'Save template'), ['class' => 'btn btn-success btn-sm', 'id' => 'save-template']),
                                            'toggleButton' => ['class' => 'btn btn-primary btn-xs', 'label' => Yii::t('contact', 'Save as a new template'), 'id' => 'toggle-new-template-popover'],
                                        ]);

                                        ?>

                                        <div id="save-template-result" class="alert fade in"
                                             style="display: none;"></div>

                                        <?php
                                        echo $form->field($templateModel, 'name')->textInput(['placeholder' => Yii::t('contact', 'New template {name}', ['name' => \common\helpers\DateHelper::getFrenchFormatDbDate(date('Y-m-d H:i'), true)])]);
                                        echo Html::hiddenInput('ContactTemplate[body]', '', ['id' => 'contacttemplate-body']);

                                        echo Html::tag('b', Yii::t('contact', 'Template'));
                                        echo Html::tag('div', '', ['id' => 'template-preview', 'class' => 'callout callout-warning']);

                                        PopoverX::end();
                                        ?>
                                    </div>

                                    <div class="col-md-5">
                                        <h4><?php echo Yii::t('contact', 'Templates'); ?></h4>

                                        <div class="row">
                                            <div class="col-md-9">

                                                <?php $items = [] ?>
                                                <?php foreach (Yii::$app->params['languagesList'] as $languageModel): ?>

                                                    <?php $items[] = [
                                                        'options' => ['id' => 'tab_' . $languageModel->id],
                                                        'label' => $languageModel->getIconHtml(),
                                                        'content' => Html::dropDownList('selectedTemplate', '', \yii\helpers\ArrayHelper::map($templates, function ($model) use ($languageModel) { return $languageModel->id . '_' . $model->id; }, function ($model) use ($languageModel) { return $model->{'name' . '_' . $languageModel->id}; }), ['prompt' => Yii::t('contact', '- Choose template to load -'), 'id' => 'selectedTemplate_' . $languageModel->id, 'class' => 'selectedTemplate form-control'])
                                                        //'active' => true
                                                    ] ?>

                                                <?php endforeach ?>

                                                <?= \kartik\tabs\TabsX::widget([
                                                    'id' => 'lang-tabs',
                                                    'items' => $items,
                                                    'position' => \kartik\tabs\TabsX::POS_ABOVE,
                                                    'encodeLabels' => false
                                                ]); ?>

                                            </div>
                                            <div class="col-md-2">
                                                <?php echo Html::button(Yii::t('contact', 'Load'), ['class' => 'btn btn-primary pull-left', 'id' => 'load-template']) ?>
                                            </div>
                                        </div>

                                        <br>
                                        <strong><?php echo Yii::t('contact', 'Template preview'); ?> :</strong>

                                        <hr/>
                                        <div id="previewTemplate"></div>

                                        <div id="templatesBody" style="display: none;">
                                            <?php foreach (Yii::$app->params['languagesList'] as $languageModel): ?>

                                                <?php foreach ($templates as $contactTemplate): ?>
                                                    <div id="template_<?php echo $languageModel->id . '_' . $contactTemplate->id; ?>" style="display: none;">
                                                        <?php echo $contactTemplate->{('body' . '_' . $languageModel->id)}; ?>
                                                    </div>
                                                <?php endforeach; ?>

                                            <?php endforeach; ?>
                                        </div>


                                    </div>
                                </div>

                            </div>

                            <div class="box-footer">
                                <div class="form-group">
                                    <?= Html::submitButton(Yii::t('admin', 'Send'), ['class' => 'btn btn-success']) ?>
                                </div>
                            </div>

                            <?php ActiveForm::end(); ?>

                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>

    </div>


<?php $this->registerJs("

    var languages = " . \yii\helpers\Json::encode(\common\helpers\ArrayHelper::map(Yii::$app->params['languagesList'], 'id', 'id')) . ";

    $('#new-template-popover').on('click.target.popoverX', function (e) {
        $('#save-template-result').removeClass('alert-success').removeClass('alert-error').hide();
        $('.field-contacttemplate-name').show();
        $('#save-template').prop('disabled', false);

        $('#contacttemplate-body').val($('#contactthread-body').val());
        $('#template-preview').html($('#contacttemplate-body').val());
    });

    var updateSavePossibility = function() {
    if ($.trim($('#contactthread-body').val()) == '') {
            $('#toggle-new-template-popover').prop('disabled', true);
        } else {
            $('#toggle-new-template-popover').prop('disabled', false);
        }
    };
    updateSavePossibility();

    $('#save-template').on('click', function() {
        var templateName = $('#contacttemplate-name').val();
        if ($.trim(templateName) == '') {
            templateName = '" . Yii::t('contact', 'New template {name}', ['name' => \common\helpers\DateHelper::getFrenchFormatDbDate(date('Y-m-d H:i'), true)]) . "'
        }

        $.ajax({
            url: '" . Url::to('/contact/manage-template/create') . "',
            dataType: 'json',
            type: 'POST',
            data: {
                'ContactTemplate[name]': templateName,
                'ContactTemplate[body]': $('#contacttemplate-body').val()
            },
            success: function(data, textStatus, XMLHttpRequest) {
                if (Boolean(data)) {

                    $.each(languages, function(index, value) {
                        $('#selectedTemplate_' + index).append(
                            $('<option></option>').val(index + '_' + data.id).html(data.name)
                        );

                        $('#templatesBody').append(
                            $('<div style=\'display:none;\'></div>').attr('id', 'template_' + index + '_' + data.id).html(data.body)
                        );
                    });

                    $('#save-template').prop('disabled', true);

                    $('.field-contacttemplate-name').hide();
                    $('#save-template-result').addClass('alert-success').html('" . Yii::t('contact', 'Template successfully saved.') . "').show();

                    $('#contacttemplate-name').val('');
                } else {
                    $('#save-template-result').addClass('alert-error').html('" . Yii::t('contact', 'Error when saving new template! Please close dialog and try again.') . "').show();
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                $('#saveStatus').addClass('error').html('" . Yii::t('contact', 'Error when saving new template! Please close dialog and try again.') . "')
            }
        });
    });

    $('.selectedTemplate').on('change', function() {
        var selection = $(this).val();

        if (Boolean(selection)) {
            $('#previewTemplate').html($('#template_' + selection).html());
        } else {
            $('#previewTemplate').html('');
        }
    });

    $('#load-template').on('click', function() {
        if ($('#template_'+ $('.tab-content div.active select').val()).length) {
            $('#contactthread-body').redactor('insert.set', $('#template_'+ $('.tab-content div.active select').val()).html());
        }
    });


 "); ?>