<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\RoomAccessRequestCV */

$this->title = $model->accessRequestID;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Room Access Request Cvs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-access-request-cv-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->accessRequestID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->accessRequestID], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'accessRequestID',
            'agreementID',
        ],
    ]) ?>

</div>
