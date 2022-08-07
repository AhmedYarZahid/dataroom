<?php

use yii\helpers\Html;

$this->title = Yii::t('admin', 'City:') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-file-text-o"></i> ' . Yii::t('admin', 'Cities'), 'url' => ['manage-cities/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>