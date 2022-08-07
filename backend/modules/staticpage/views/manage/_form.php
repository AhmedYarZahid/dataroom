<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\staticpage\models\StaticPage;
use backend\modules\comments\models\CommentBundle;
use backend\modules\comments\widgets\CommentAdmin\CommentAdmin;
use backend\modules\metatags\models\MetaTags;
use backend\modules\metatags\widgets\MetaTagsAdmin\MetaTagsAdmin;

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
                    <?= $form->field($model, 'title')->textInput() ?>
                    <?= $form->field($model, 'body')->widget(common\widgets\imperavi\Widget::classname(), [
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

                    <?= $form->field($model, 'type')->dropDownList(StaticPage::getTypes()); ?>
                </div>

                <hr class="admin-widgets-separator">

                <?= CommentAdmin::widget([
                    'nodeType' => CommentBundle::NODE_TYPE_STATICPAGE,
                    'nodeID' => $model->id,
                    'form' => $form,
                ]); ?>

                <hr class="admin-widgets-separator">

                <?= MetaTagsAdmin::widget([
                    'nodeType' => MetaTags::NODE_TYPE_STATICPAGE,
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