<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\contact\models\ContactTemplate;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">

                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false,
                    'options' => ['enctype' => 'multipart/form-data'],

                ]); ?>

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
        console.log($(this).prop('id'));
            $('#lang-tabs a[href=\"#' + $(this).prop('id') + '\"]').tab('show');

            return false;
        }
    });

"); ?>