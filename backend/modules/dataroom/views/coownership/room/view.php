<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\RoomCoownership */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Room Coownerships'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-coownership-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id, 'roomID' => $model->roomID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id, 'roomID' => $model->roomID], [
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
            'propertyType',
            'address',
            'zip',
            'city',
            'regionID',
            'latitude',
            'longitude',
            'missionEndDate',
            'coownershipName',
            'lotsNumber',
            'coownersNumber',
            'mainLotsNumber',
            'secondaryLotsNumber',
            'employeesNumber',
            'lastFinancialYearApprovedAccountsID',
            'constructionYear',
            'totalFloorsNumber',
            'isElevator',
            'heatingType',
            'heatingEnergy',
            'quickDescription:ntext',
            'detailedDescription:ntext',
            'keywords:ntext',
            'procedure',
            'procedureContact:ntext',
            'firstName',
            'lastName',
            'phone',
            'fax',
            'phoneMobile',
            'email:email',
            'availabilityDate',
            'homePresence',
            'visibility',
            'offerAcceptanceCondition:ntext',
            'individualAssetsPresence',
            'presenceEndDate',
            'adPosition',
        ],
    ]) ?>

</div>
