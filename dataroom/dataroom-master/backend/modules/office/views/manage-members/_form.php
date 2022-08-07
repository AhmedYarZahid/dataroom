<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
use common\helpers\ArrayHelper;

?>

<div class="member-form">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">

                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false,
                ]); ?>

                <div class="box-body">
                    <?= $form->field($model, 'firstName')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'lastName')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'image')->widget(FileInput::classname(), [
                        'options' => ['accept' => 'image/*'],
                        'pluginOptions' => [
                            'previewFileType' => 'image',
                            'showUpload' => false,
                            'showCaption' => false,
                            'browseClass' => 'btn btn-primary btn-block',
                            'removeClass' => 'btn btn-default btn-remove',
                        ],
                    ]); ?>
                    <?php if ($model->getImagePath(true)): ?>
                        <?= Html::img($model->getImagePath(true)) ?>
                        <br><br>
                    <?php endif ?>
                    
                    <?php //echo $form->field($model, 'url', ['options' => ['class' => 'form-group url-page']])->dropDownList(ArrayHelper::map(\lateos\trendypage\models\TrendyPage::getList(), 'id', 'title'), ['prompt' => ''])->label(Yii::t('admin', 'Trendy Page'))->hint(Yii::t('admin', 'Please choose trendy page from the list')) ?>

                    <?= $form->field($model, 'body')->widget(common\widgets\imperavi\Widget::classname(), [
                        'settings' => [
                            //'buttons' => "js:['formatting', '|', 'bold', 'italic', 'deleted', '|', 'fontcolor', 'alignment', 'horizontalrule']",
                            //'lang' => 'fr',
                            'buttonSource' => true,
                            'minHeight' => 400,
                            'linebreaks' => false,
                            'replaceDivs' => false,

                            'plugins' => [
                                'fullscreen',
                            ]
                        ],
                    ]); ?>

                    <?= $form->field($model, 'officeIds')->widget(\kartik\select2\Select2::classname(), [
                        'data' => $model->officeList(),
                        'options' => ['multiple' => true],
                        'pluginOptions' => [
                            'allowClear' => false,
                        ],
                    ]) ?>
                    
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