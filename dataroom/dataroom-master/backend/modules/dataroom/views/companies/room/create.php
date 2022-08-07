<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\document\models\Document */

$this->title = Yii::t('admin', 'Create {modelClass}', [
    'modelClass' => Yii::t('admin', 'Room'),
]);
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-table"></i> AJArepreneurs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="room-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', ['model' => $model, 'room' => $room]) ?>

</div>
