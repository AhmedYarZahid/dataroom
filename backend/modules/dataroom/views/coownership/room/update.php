<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\RoomRealEstate */

$this->title = Yii::t('admin', 'Update {modelClass}', [
    'modelClass' => Yii::t('admin', 'Room'),
]);
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-table"></i>AJAsyndic', 'url' => [$this->context->roomType . '/room/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-coownership-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'room' => $room
    ]) ?>

</div>
