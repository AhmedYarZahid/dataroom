<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('admin', 'User:') .' ' . $model->getFullName() . ' [#' . $model->id . ']';
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-users"></i> ' . Yii::t('admin', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-user"></i> ' .  $model->getFullName() . ' [#' . $model->id . ']', 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Update');
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'profiles' => $profiles,
        'photosInitData' => $photosInitData,
    ]) ?>

</div>
