<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\comments\models\CommentBundle;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model CommentBundle */
?>

<div class="comment-bundle-form">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">

                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false,
                    'options' => ['enctype' => 'multipart/form-data'],

                ]); ?>

                <div class="box-body">
                    <?= $form->field($model, 'isActive')->checkbox() ?>
                    <?= $form->field($model, 'isNewCommentsAllowed')->checkbox() ?>
                </div>

                <div class="box-footer">
                    <div class="form-group">
                        <?= Html::submitButton($model->isNewRecord ? Yii::t('admin', 'Create') :
                            Yii::t('admin', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>