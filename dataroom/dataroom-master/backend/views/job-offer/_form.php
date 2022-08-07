<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\tabs\TabsX;

/* @var $this yii\web\View */
/* @var $model common\models\JobOffer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="job-offer-form">
    <div class="row">
        <div class="col-md-8">
            <div class="box box-primary">

                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false,
                    'options' => ['enctype' => 'multipart/form-data'],

                ]); ?>

                <div class="box-header">
                    <h3 class="box-title"><?= Yii::t('admin', 'Data') ?></h3>
                </div>

                <div class="box-body">

                    <?php $items = [] ?>
                    <?php foreach (Yii::$app->params['languagesList'] as $languageModel): ?>

                        <?php $items[] = [
                            'label' => $languageModel->getIconHtml(),
                            'content' => $this->render('_form-lang-fields', ['model' => $model, 'languageModel' => $languageModel, 'form' => $form]),
                            //'active' => true
                        ] ?>

                    <?php endforeach ?>

                    <?= TabsX::widget([
                        'id' => 'lang-tabs',
                        'items' => $items,
                        'position' => TabsX::POS_ABOVE,
                        'encodeLabels' => false
                    ]); ?>

                    <hr style="margin-top: 5px;">

                    <?= $form->field($model, 'contractType')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'salary')->textInput(['maxlength' => true]) ?>

                    <?php //$form->field($model, 'currency')->dropDownList(\common\models\JobOffer::getCurrencyList(), ['prompt' => '']) ?>

                    <?= $form->field($model, 'contactEmail')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'startDate')->widget(\kartik\datecontrol\DateControl::classname(), [
                        'widgetOptions' => [
                            'removeButton' => false,
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'autoclose' => true,
                            ]
                        ],
                    ]); ?>

                    <?= $form->field($model, 'expiryDate')->widget(\kartik\datecontrol\DateControl::classname(), [
                        'widgetOptions' => [
                            'removeButton' => false,
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'autoclose' => true,
                            ]
                        ],
                    ]); ?>

                    <?= $form->field($model, 'publicationDate')->widget(\kartik\datecontrol\DateControl::classname(), [
                        'widgetOptions' => [
                            'removeButton' => false,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'todayHighlight' => true,
                            ]
                        ],
                    ]); ?>
                    <?= Html::a(Yii::t('news', 'Today'), 'javascript:void(0);', ['id' => 'today-date', 'class' => 'btn btn-warning btn-xs']) ?>
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
    // Activate tab with errors
    $('#lang-tabs-container .tab-pane').each(function() {
        if ($(this).find('.has-error').length) {
            $('#lang-tabs a[href=\"#' + $(this).prop('id') + '\"]').tab('show');

            return false;
        }
    });

    $('#today-date').click(function() {
        $('#joboffer-publicationdate').val('" . date('Y-m-d') . "');
        $('#joboffer-publicationdate-disp').val('" . \common\helpers\DateHelper::getFrenchFormatDbDate(date('Y-m-d')) . "');
    });

"); ?>