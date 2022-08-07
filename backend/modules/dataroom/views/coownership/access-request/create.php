<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\RoomAccessRequestCoownership */

$this->title = Yii::t('app', 'Create Room Access Request Coownership');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Room Access Request Coownerships'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-access-request-coownership-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
