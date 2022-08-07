<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\notify\models\Notify;
use kartik\popover\PopoverX;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-warning">

                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false,
                    'options' => ['enctype' => 'multipart/form-data'],

                ]); ?>

                <div class="box-body">

                    <?= $form->field($model, 'eventID')
                        ->dropDownList(Notify::getEventFilter(), ['prompt' => Yii::t('notify', ' - Please select - ')])
                        ->hint(PopoverX::widget([
                            'id' => 'view-vars-popover',
                            'size' => PopoverX::SIZE_MEDIUM,
                            'header' => Yii::t('notify', 'Available tags'),
                            'placement' => PopoverX::ALIGN_RIGHT,
                            'content' => '',
                            'toggleButton' => [
                                'label' => Yii::t('admin', 'Show available tags'),
                                'id' => 'view-vars',
                                'class' => 'btn-xs btn-link'
                            ],
                    ])) ?>

                    <?php $items = [] ?>
                    <?php foreach (Yii::$app->params['languagesList'] as $languageModel): ?>

                        <?php $items[] = [
                            'label' => $languageModel->getIconHtml(),
                            'content' => $this->render('_form-lang-fields', ['model' => $model, 'languageModel' => $languageModel, 'form' => $form]),
                            //'active' => true
                        ] ?>

                    <?php endforeach ?>

                    <?= \kartik\tabs\TabsX::widget([
                        'id' => 'lang-tabs',
                        'items' => $items,
                        'position' => \kartik\tabs\TabsX::POS_ABOVE,
                        'encodeLabels' => false
                    ]); ?>

                    <hr style="margin-top: 5px;">

                    <?= $form->field($model, 'priority')->textInput() ?>
                    <?= $form->field($model, 'isDefault')->checkbox() ?>
                    <?= $form->field($model, 'putToQueue')->checkbox() ?>
                </div>

                <div class="box-footer">
                    <div class="form-group">
                        <?= Html::submitButton($model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

<?php $this->registerJs("
    var eventsList = ". \yii\helpers\Json::encode(Notify::$eventCaptions) . ";

    $('#view-vars').on('click.target.popoverX', function (e) {
        var eventID = $('#notify-eventid').val();

        var content = '';
        if (!eventID) {
            content = 'No variables';
        } else {
            $.each(eventsList[eventID]['tags'], function(key, value) {
                content += '<p style=\'padding: 4px 0px 3px 0px; margin:0;\'>' + key + ' - ' + value + '</p>';
            });
        }

        $('#view-vars-popover .popover-content').html(content);
    });

    // Activate tab with errors
    $('#lang-tabs-container .tab-pane').each(function() {
        if ($(this).find('.has-error').length) {
            $('#lang-tabs a[href=\"#' + $(this).prop('id') + '\"]').tab('show');

            return false;
        }
    });
"); ?>