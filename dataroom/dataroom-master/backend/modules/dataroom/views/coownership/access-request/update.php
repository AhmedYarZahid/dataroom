<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\RoomAccessRequestCoownership */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Room Access Request Coownership',
]) . $model->accessRequestID;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Room Access Request Coownerships'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->accessRequestID, 'url' => ['view', 'id' => $model->accessRequestID]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="room-access-request-coownership-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
