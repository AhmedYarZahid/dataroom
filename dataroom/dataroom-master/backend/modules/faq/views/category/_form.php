<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model backend\modules\faq\models\FaqCategory */

?>

<div class="faq-category-form">
    <div class="box <?= $model->isNewRecord ? 'box-info' : 'box-solid' ?>">

        <?php $form = ActiveForm::begin([
            'enableClientValidation' => false,
            'validateOnSubmit' => false,
            'options' => ['enctype' => 'multipart/form-data'],

        ]); ?>

        <div class="box-body">
            <?= $form->field($model, 'title')->textInput() ?>
        </div>

        <div class="box-footer">
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
