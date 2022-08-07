<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\search\RoomAccessRequestRealEstateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="room-access-request-real-estate-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'accessRequestID') ?>

    <?= $form->field($model, 'personType') ?>

    <?= $form->field($model, 'candidatePresentation') ?>

    <?= $form->field($model, 'identityCardID') ?>

    <?= $form->field($model, 'cvID') ?>

    <?php // echo $form->field($model, 'lastTaxDeclarationID') ?>

    <?php // echo $form->field($model, 'companyPresentation') ?>

    <?php // echo $form->field($model, 'kbisID') ?>

    <?php // echo $form->field($model, 'registrationsUpdatedStatusID') ?>

    <?php // echo $form->field($model, 'latestCertifiedAccountsID') ?>

    <?php // echo $form->field($model, 'capitalAllocationID') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
