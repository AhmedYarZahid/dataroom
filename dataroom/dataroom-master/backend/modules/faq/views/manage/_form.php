<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="faq-item-form">
    <div class="row">
        <div class="col-md-12">
            <div class="box <?= $model->isNewRecord ? 'box-info' : 'box-solid' ?>">

                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false,
                    'options' => ['enctype' => 'multipart/form-data'],

                ]); ?>

                <div class="box-body">
                    <?= $form->field($model, 'question')->textInput() ?>

                    <?= $form->field($model, 'answer')->widget(common\widgets\imperavi\Widget::classname(), [
                        'settings' => [
                            //'buttons' => "js:['formatting', '|', 'bold', 'italic', 'deleted', '|', 'fontcolor', 'alignment', 'horizontalrule']",
                            //'lang' => 'fr',
                            'buttonSource' => true,
                            'minHeight' => 400,
                            'linebreaks' => false,
                            'replaceDivs' => false,
                            'imageUpload' => \yii\helpers\Url::to(['upload-image']),
                            'imageUploadFields' => [
                                Yii::$app->request->csrfParam => Yii::$app->request->getCsrfToken()
                            ],
                            'imageManagerJson' => \yii\helpers\Url::to(['get-images']),

                            'plugins' => [
                                'source',
                                'imagemanager',
                                'table',
                                'fontcolor',
                                'fullscreen',
                            ]
                        ],
                    ]); ?>

                    <?= $form->field($model, 'faqCategoryID')->hiddenInput()->label(false) ?>
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
