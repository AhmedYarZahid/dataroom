<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Language */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="language-form">
    <div class="row">
        <div class="col-md-8">
            <div class="box box-primary">

                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false,
                    'options' => ['enctype' => 'multipart/form-data'],

                ]); ?>

                <div class="box-header">
                    <h3 class="box-title"><?= Yii::t('admin', 'Language Data') ?></h3>
                </div>

                <div class="box-body">

                    <?= $form->field($model, 'id')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'locale')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'isDefault')->checkbox() ?>

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