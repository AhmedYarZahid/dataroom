<?php

/* @var $this yii\web\View */
/* @var $model common\models\JobOffer */
/* @var $languageModel \common\models\Language */

?>

<?= $form->field($model, $model->getFormAttributeName('title', $languageModel->id))->textInput(['maxlength' => true]) ?>

<?= $form->field($model, $model->getFormAttributeName('location', $languageModel->id))->textInput(['maxlength' => true]) ?>

<?= $form->field($model, $model->getFormAttributeName('description', $languageModel->id))->textarea(['rows' => 5]) ?>

<?= $form->field($model, $model->getFormAttributeName('skills', $languageModel->id))->textarea(['rows' => 3]) ?>
