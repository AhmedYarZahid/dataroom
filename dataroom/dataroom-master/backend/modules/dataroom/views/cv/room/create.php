<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\RoomRealEstate */
/* @var $room \backend\modules\dataroom\models\Room */

$this->title = Yii::t('admin', 'Create {modelClass}', [
    'modelClass' => Yii::t('admin', 'Room'),
]);
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-table"></i>AJAreclassement', 'url' => [$this->context->roomType . '/room/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="room-cv-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'room' => $room
    ]) ?>

</div>
