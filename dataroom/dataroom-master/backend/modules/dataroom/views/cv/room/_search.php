<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\search\RoomCVSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="room-cv-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'roomID') ?>

    <?= $form->field($model, 'companyName') ?>

    <?= $form->field($model, 'activityDomainID') ?>

    <?= $form->field($model, 'candidateProfile') ?>

    <?php // echo $form->field($model, 'functionID') ?>

    <?php // echo $form->field($model, 'subFunctionID') ?>

    <?php // echo $form->field($model, 'firstName') ?>

    <?php // echo $form->field($model, 'lastName') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'cvID') ?>

    <?php // echo $form->field($model, 'departmentID') ?>

    <?php // echo $form->field($model, 'regionID') ?>

    <?php // echo $form->field($model, 'seniority') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
