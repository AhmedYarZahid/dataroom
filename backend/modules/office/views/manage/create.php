<?php

use yii\helpers\Html;

$this->title = Yii::t('admin', 'Create new office');
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-file-text-o"></i> ' . Yii::t('admin', 'Offices'), 'url' => ['manage/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="offices-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
