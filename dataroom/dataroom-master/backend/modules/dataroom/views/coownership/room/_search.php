<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\search\RoomCoownershipSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="room-coownership-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'roomID') ?>

    <?= $form->field($model, 'propertyType') ?>

    <?= $form->field($model, 'address') ?>

    <?= $form->field($model, 'zip') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'regionID') ?>

    <?php // echo $form->field($model, 'latitude') ?>

    <?php // echo $form->field($model, 'longitude') ?>

    <?php // echo $form->field($model, 'missionEndDate') ?>

    <?php // echo $form->field($model, 'coownershipName') ?>

    <?php // echo $form->field($model, 'lotsNumber') ?>

    <?php // echo $form->field($model, 'coownersNumber') ?>

    <?php // echo $form->field($model, 'mainLotsNumber') ?>

    <?php // echo $form->field($model, 'secondaryLotsNumber') ?>

    <?php // echo $form->field($model, 'employeesNumber') ?>

    <?php // echo $form->field($model, 'lastFinancialYearApprovedAccountsID') ?>

    <?php // echo $form->field($model, 'constructionYear') ?>

    <?php // echo $form->field($model, 'totalFloorsNumber') ?>

    <?php // echo $form->field($model, 'isElevator') ?>

    <?php // echo $form->field($model, 'heatingType') ?>

    <?php // echo $form->field($model, 'heatingEnergy') ?>

    <?php // echo $form->field($model, 'quickDescription') ?>

    <?php // echo $form->field($model, 'detailedDescription') ?>

    <?php // echo $form->field($model, 'keywords') ?>

    <?php // echo $form->field($model, 'procedure') ?>

    <?php // echo $form->field($model, 'procedureContact') ?>

    <?php // echo $form->field($model, 'firstName') ?>

    <?php // echo $form->field($model, 'lastName') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'fax') ?>

    <?php // echo $form->field($model, 'phoneMobile') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'availabilityDate') ?>

    <?php // echo $form->field($model, 'homePresence') ?>

    <?php // echo $form->field($model, 'visibility') ?>

    <?php // echo $form->field($model, 'offerAcceptanceCondition') ?>

    <?php // echo $form->field($model, 'individualAssetsPresence') ?>

    <?php // echo $form->field($model, 'presenceEndDate') ?>

    <?php // echo $form->field($model, 'adPosition') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
