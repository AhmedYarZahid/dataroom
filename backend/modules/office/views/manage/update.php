<?php

use yii\helpers\Html;

$this->title = Yii::t('admin', 'Office') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-file-text-o"></i> ' . Yii::t('admin', 'Offices'), 'url' => ['manage/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="office-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>