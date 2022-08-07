<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\search\ProposalCoownershipSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="proposal-coownership-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'proposalID') ?>

    <?= $form->field($model, 'documentID') ?>

    <?= $form->field($model, 'companyName') ?>

    <?= $form->field($model, 'fullName') ?>

    <?= $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'kbisID') ?>

    <?php // echo $form->field($model, 'cniID') ?>

    <?php // echo $form->field($model, 'businessCardID') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
