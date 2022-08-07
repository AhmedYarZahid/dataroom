<?php

use yii\bootstrap\Tabs;
use yii\helpers\Html;
use kartik\form\ActiveForm;

$errors = array_merge(array_keys($model->getErrors()), array_keys($room->getErrors()));

$generalFields = [
    'ca', 'userEmail', 'userName', 'title', 'mission', 'marketing', 'status', 'propertyType',
    'propertySubType', 'libAd', 'city', 'address', 'zip', 'countryID', 'regionID', 'latitude', 'longitude',
    'constructionYear', 'totalFloorsNumber', 'floorNumber', 'area', 'isDuplex', 'isElevator', 'roomsNumber',
    'bedroomsNumber', 'bathroomsNumber', 'showerRoomsNumber', 'kitchensNumber', 'toiletsNumber', 'isSeparateToilet',
    'separateToiletsNumber', 'heatingType', 'heatingEnergy', 'proximity', 'quickDescription', 'detailedDescription',
    'keywords', 'sellingPrice', 'totalPrice', 'totalPriceFrequency', 'charges', 'chargesFrequency', 'currency',
    'propertyTax', 'housingTax', 'condominiumLotsNumber', 'adLotNumber', 'individualAssetsPresence', 'presenceEndDate',
    'adPosition', 'adminID'
];
$procedureFields = [
    'procedure', 'procedureContact', 'firstName', 'lastName', 'phone',
    'fax', 'phoneMobile', 'email',
    'availabilityDate', 'homePresence',
    'visibility', 'offerAcceptanceCondition',
    'publicationDate', 'archivationDate', 'expirationDate', 'createdDate',
];

$generalHasErrors = !empty(array_intersect($generalFields, $errors));
$procedureHasErrors = !empty(array_intersect($procedureFields, $errors));

?>

<div class="room-form">
    <div class="row">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin([
                'enableClientValidation' => false,
                'validateOnSubmit' => false,
                'options' => ['enctype' => 'multipart/form-data'],
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => [
                    'labelSpan' => 4
                ]
            ]); ?>

            <?= Tabs::widget([
                'items' => [
                    [
                        'label' => 'Général',
                        'content' => $this->render('_general', compact('form', 'room', 'model')),
                        'active' => true,
                        'headerOptions' => $generalHasErrors ? ['class' => 'error'] : [],
                    ],
                    [
                        'label' => 'Procédure',
                        'content' => $this->render('_procedure', compact('form', 'room', 'model')),
                        'headerOptions' => $procedureHasErrors ? ['class' => 'error'] : [],
                    ],
                ],
            ]); ?>

            <div class="form-group">
                <div class="col-sm-12">
                    <?php $label = $model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Update'); ?>
                    <?= Html::submitButton($label, ['class' => 'btn btn-lg btn-primary btn-block']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>