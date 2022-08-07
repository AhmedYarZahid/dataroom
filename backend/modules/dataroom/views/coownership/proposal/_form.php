<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\ProposalCoownership */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="proposal-coownership-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'proposalID')->textInput() ?>

    <?= $form->field($model, 'documentID')->textInput() ?>

    <?= $form->field($model, 'companyName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fullName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'kbisID')->textInput() ?>

    <?= $form->field($model, 'cniID')->textInput() ?>

    <?= $form->field($model, 'businessCardID')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
