<?php
/* @var $model \yii\db\ActiveRecord */
/* @var $attributes array */
?>

<div>
    <?= \kartik\detail\DetailView::widget([
    'model' => $model,
    'hover' => true,
    'mode' => \kartik\detail\DetailView::MODE_VIEW,
    'attributes' => $attributes
]) ?>
</div>