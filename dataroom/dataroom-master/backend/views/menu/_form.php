<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Menu */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-form">
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

                    <?= \kartik\tabs\TabsX::widget([
                        'id' => 'lang-tabs',
                        'items' => $items,
                        'position' => \kartik\tabs\TabsX::POS_ABOVE,
                        'encodeLabels' => false
                    ]); ?>

                    <hr style="margin-top: 5px;">

                    <?= $form->field($model, 'entity')->dropDownList(\common\models\Menu::getEntities(), ['prompt' => '']) ?>

                    <?= $form->field($model, 'url', ['options' => ['class' => 'form-group url-page']])->dropDownList(ArrayHelper::map(\lateos\trendypage\models\TrendyPage::getList(), 'id', 'title'), ['prompt' => ''])->label(Yii::t('admin', 'Trendy Page'))->hint(Yii::t('admin', 'Please choose trendy page from the list')) ?>
                    <?= $form->field($model, 'url', ['options' => ['class' => 'form-group url-form']])->dropDownList(ArrayHelper::map(\lateos\formpage\models\FormPage::getList(), 'id', 'title'), ['prompt' => ''])->label(Yii::t('admin', 'Form Page'))->hint(Yii::t('admin', 'Please choose form page from the list')) ?>
                    <?= $form->field($model, 'url', ['options' => ['class' => 'form-group url-uri']])->textInput(['maxlength' => true])->label(Yii::t('admin', 'URI'))->hint(Yii::t('admin', 'Please provide link to internal page without domain. E.g.: site/signup')) ?>
                    <?= $form->field($model, 'url', ['options' => ['class' => 'form-group url-url']])->textInput(['maxlength' => true])->label(Yii::t('admin', 'URL'))->hint(Yii::t('admin', 'Please provide link to external page. E.g.: https://www.google.com')) ?>

                    <?= Html::activeHiddenInput($model, 'parentID') ?>
                    <?= Html::activeHiddenInput($model, 'rank') ?>

                    <?= $form->field($model, 'target')->checkbox(['value' => '_blank', 'uncheck' => '',
                        'label' => Yii::t('admin', 'Open in new tab')]) ?>

                    <?= $form->field($model, 'isActive')->checkbox() ?>

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
    function toggleControls(activeControl) {
        var state = false,
            controls = {
                '" . \common\models\Menu::ENTITY_TRENDY_PAGE . "':$('.url-page'),
                '" . \common\models\Menu::ENTITY_FORM_PAGE . "':$('.url-form'),
                '" . \common\models\Menu::ENTITY_PAGE . "':$('.url-uri'),
                '" . \common\models\Menu::ENTITY_URL . "':$('.url-url'),
            };
        
        Object.keys(controls).forEach(function(controlKey) {
            var state = (controlKey == activeControl);
            
            controls[controlKey].find('.form-control').prop('disabled', !state).end().toggle(state);
        });
    }

    $('body').on('change', '#menu-entity', function() {
        toggleControls($(this).val());
    });
    $('#menu-entity').trigger('change');
"); ?>

<?php $this->registerJs("
    // Activate tab with errors
    $('#lang-tabs-container .tab-pane').each(function() {
        if ($(this).find('.has-error').length) {
            $('#lang-tabs a[href=\"#' + $(this).prop('id') + '\"]').tab('show');

            return false;
        }
    });

"); ?>