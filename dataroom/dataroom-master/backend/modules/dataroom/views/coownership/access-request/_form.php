<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\RoomAccessRequestCoownership */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="room-access-request-coownership-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'accessRequestID')->textInput() ?>

    <?= $form->field($model, 'personType')->dropDownList([ 'physical' => 'Physical', 'legal' => 'Legal', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'candidatePresentation')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'identityCardID')->textInput() ?>

    <?= $form->field($model, 'cvID')->textInput() ?>

    <?= $form->field($model, 'lastTaxDeclarationID')->textInput() ?>

    <?= $form->field($model, 'coownershipManagementReferenceID')->textInput() ?>

    <?= $form->field($model, 'groupPresentation')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'kbisID')->textInput() ?>

    <?= $form->field($model, 'latestCertifiedAccountsID')->textInput() ?>

    <?= $form->field($model, 'capitalAllocationID')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
