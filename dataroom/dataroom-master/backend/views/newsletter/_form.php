<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Newsletter */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="newsletter-form">
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

                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'firstName')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'lastName')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'profession')->dropDownList($model->professionList(), ['prompt' => '']) ?>

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