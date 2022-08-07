<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\RoomRealEstate */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Room Real Estates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-real-estate-view">

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
            'mission',
            'marketing',
            'status',
            'propertyType',
            'propertySubType',
            'libAd',
            'address',
            'zip',
            'city',
            'countryID',
            'regionID',
            'latitude',
            'longitude',
            'constructionYear',
            'totalFloorsNumber',
            'floorNumber',
            'area',
            'isDuplex',
            'isElevator',
            'roomsNumber',
            'bedroomsNumber',
            'bathroomsNumber',
            'showerRoomsNumber',
            'kitchensNumber',
            'toiletsNumber',
            'isSeparateToilet',
            'separateToiletsNumber',
            'heatingType',
            'heatingEnergy',
            'proximity',
            'quickDescription:ntext',
            'detailedDescription:ntext',
            'keywords:ntext',
            'totalPrice',
            'totalPriceFrequency',
            'charges',
            'chargesFrequency',
            'currency',
            'propertyTax',
            'housingTax',
            'condominiumLotsNumber',
            'adLotNumber',
            'procedure',
            'procedureContact:ntext',
            'firstName',
            'lastName',
            'phone',
            'fax',
            'phoneMobile',
            'email:email',
            'openingDate',
            'closingDate',
            'tendersSubmissionDeadline',
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
