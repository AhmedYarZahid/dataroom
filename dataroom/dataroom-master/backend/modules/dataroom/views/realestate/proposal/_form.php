<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\ProposalRealEstate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="proposal-real-estate-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'proposalID')->textInput() ?>

    <?= $form->field($model, 'documentID')->textInput() ?>

    <?= $form->field($model, 'firstName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lastName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'kbisID')->textInput() ?>

    <?= $form->field($model, 'cniID')->textInput() ?>

    <?= $form->field($model, 'balanceSheetID')->textInput() ?>

    <?= $form->field($model, 'taxNoticeID')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
