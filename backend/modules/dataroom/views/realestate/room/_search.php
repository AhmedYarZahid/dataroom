<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\dataroom\models\search\RoomRealEstateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="room-real-estate-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'roomID') ?>

    <?= $form->field($model, 'mission') ?>

    <?= $form->field($model, 'marketing') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'propertyType') ?>

    <?php // echo $form->field($model, 'propertySubType') ?>

    <?php // echo $form->field($model, 'libAd') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'zip') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'countryID') ?>

    <?php // echo $form->field($model, 'regionID') ?>

    <?php // echo $form->field($model, 'latitude') ?>

    <?php // echo $form->field($model, 'longitude') ?>

    <?php // echo $form->field($model, 'constructionYear') ?>

    <?php // echo $form->field($model, 'totalFloorsNumber') ?>

    <?php // echo $form->field($model, 'floorNumber') ?>

    <?php // echo $form->field($model, 'area') ?>

    <?php // echo $form->field($model, 'isDuplex') ?>

    <?php // echo $form->field($model, 'isElevator') ?>

    <?php // echo $form->field($model, 'roomsNumber') ?>

    <?php // echo $form->field($model, 'bedroomsNumber') ?>

    <?php // echo $form->field($model, 'bathroomsNumber') ?>

    <?php // echo $form->field($model, 'showerRoomsNumber') ?>

    <?php // echo $form->field($model, 'kitchensNumber') ?>

    <?php // echo $form->field($model, 'toiletsNumber') ?>

    <?php // echo $form->field($model, 'isSeparateToilet') ?>

    <?php // echo $form->field($model, 'separateToiletsNumber') ?>

    <?php // echo $form->field($model, 'heatingType') ?>

    <?php // echo $form->field($model, 'heatingEnergy') ?>

    <?php // echo $form->field($model, 'proximity') ?>

    <?php // echo $form->field($model, 'quickDescription') ?>

    <?php // echo $form->field($model, 'detailedDescription') ?>

    <?php // echo $form->field($model, 'keywords') ?>

    <?php // echo $form->field($model, 'totalPrice') ?>

    <?php // echo $form->field($model, 'totalPriceFrequency') ?>

    <?php // echo $form->field($model, 'charges') ?>

    <?php // echo $form->field($model, 'chargesFrequency') ?>

    <?php // echo $form->field($model, 'currency') ?>

    <?php // echo $form->field($model, 'propertyTax') ?>

    <?php // echo $form->field($model, 'housingTax') ?>

    <?php // echo $form->field($model, 'condominiumLotsNumber') ?>

    <?php // echo $form->field($model, 'adLotNumber') ?>

    <?php // echo $form->field($model, 'procedure') ?>

    <?php // echo $form->field($model, 'procedureContact') ?>

    <?php // echo $form->field($model, 'firstName') ?>

    <?php // echo $form->field($model, 'lastName') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'fax') ?>

    <?php // echo $form->field($model, 'phoneMobile') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'openingDate') ?>

    <?php // echo $form->field($model, 'closingDate') ?>

    <?php // echo $form->field($model, 'tendersSubmissionDeadline') ?>

    <?php // echo $form->field($model, 'availabilityDate') ?>

    <?php // echo $form->field($model, 'homePresence') ?>

    <?php // echo $form->field($model, 'visibility') ?>

    <?php // echo $form->field($model, 'offerAcceptanceCondition') ?>

    <?php // echo $form->field($model, 'individualAssetsPresence') ?>

    <?php // echo $form->field($model, 'presenceEndDate') ?>

    <?php // echo $form->field($model, 'adPosition') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
