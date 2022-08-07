<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\news\models\News;
use backend\modules\comments\models\CommentBundle;
use backend\modules\comments\widgets\CommentAdmin\CommentAdmin;
use backend\modules\metatags\models\MetaTags;
use backend\modules\metatags\widgets\MetaTagsAdmin\MetaTagsAdmin;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model News */
?>

<div class="news-form">
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

                    <hr style="margin-top: 5px;">

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
                    <?= Html::a(Yii::t('news', 'Today'), 'javascript:void(0);', ['id' => 'today-date', 'class' => 'btn btn-warning btn-xs']) ?>
                    <?= Html::a(Yii::t('news', 'Void'), 'javascript:void(0);', ['id' => 'empty-date', 'class' => 'btn btn-danger btn-xs']); ?>
                    <br><br>
                    
                    <?= $form->field($model, 'category')->dropDownList($model->categoryList()) ?>
                    
                    <?= $form->field($model, 'isActive')->checkbox() ?>

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

                </div>

                <hr class="admin-widgets-separator">

                <?= CommentAdmin::widget([
                    'nodeType' => CommentBundle::NODE_TYPE_NEWS,
                    'nodeID' => $model->id,
                    'form' => $form,
                ]); ?>

                <hr class="admin-widgets-separator">

                <?= MetaTagsAdmin::widget([
                    'nodeType' => MetaTags::NODE_TYPE_NEWS,
                    'nodeID' => $model->id,
                    'form' => $form,
                ]); ?>

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
    $('#today-date').click(function() {
        $('#news-publishdate').val('" . date('Y-m-d') . "');
        $('#news-publishdate-disp').val('" . \common\helpers\DateHelper::getFrenchFormatDbDate(date('Y-m-d')) . "');
    });

    $('#empty-date').click(function() {
        $('#news-publishdate').val('');
        $('#news-publishdate-disp').val('');
    });

    // Activate tab with errors
    $('#lang-tabs-container .tab-pane').each(function() {
        if ($(this).find('.has-error').length) {
            $('#lang-tabs a[href=\"#' + $(this).prop('id') + '\"]').tab('show');

            return false;
        }
    });

"); ?>