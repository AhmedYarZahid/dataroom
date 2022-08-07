<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\RoomAccessRequestCV */

$this->title = Yii::t('app', 'Create Room Access Request Cv');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Room Access Request Cvs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-access-request-cv-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
