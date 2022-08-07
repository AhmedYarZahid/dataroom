<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\RoomCV */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Room Cvs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-cv-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
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
            'id',
            'roomID',
            'companyName',
            'activityDomainID',
            'candidateProfile:ntext',
            'functionID',
            'subFunctionID',
            'firstName',
            'lastName',
            'address',
            'email:email',
            'phone',
            'cvID',
            'departmentID',
            'regionID',
            'seniority',
        ],
    ]) ?>

</div>
