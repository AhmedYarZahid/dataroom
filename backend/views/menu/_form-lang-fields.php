<?php

/* @var $this yii\web\View */
/* @var $model \common\models\Menu */
/* @var $languageModel \common\models\Language */

?>

<?= $form->field($model, $model->getFormAttributeName('title', $languageModel->id))->textInput(['maxlength' => true]) ?>