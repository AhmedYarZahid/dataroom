<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\RoomAccessRequestRealEstate */

$this->title = Yii::t('app', 'Create Room Access Request Real Estate');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Room Access Request Real Estates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-access-request-real-estate-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
