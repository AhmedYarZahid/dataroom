<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\datecontrol\DateControl;
use kartik\widgets\FileInput;
use frontend\assets\SmartPhotoAsset;
use backend\modules\dataroom\models\RoomCoownership;
use common\helpers\ArrayHelper;

/* @var $model RoomCoownership */

$this->title = $model->room->title;
$disabledMode = !empty($disabledMode);

$fileInputOptions = [
    'options' => [
        'multiple' => false
    ],
    'pluginOptions' => [
        'previewFileType' => 'any',
        'showPreview' => false,
        'showCaption' => true,
        'showRemove' => false,
        'showUpload' => false,
        'allowedFileExtensions' => ['pdf','doc','docx','txt','jpg','jpeg','gif','png'],
        'browseClass' => 'btn btn-primary btn-block',
        'removeClass' => 'btn btn-default btn-remove',
    ],
];

$dateControlOptions = [
    'displayFormat' => 'php:d/m/Y',
    'saveFormat' => 'php:Y-m-d',
    'widgetOptions' => [
        'removeButton' => false,
        'options' => ['placeholder' => '', 'disabled' => $disabledMode],
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
            //'startDate' => '+0d',
        ]
    ],
];

$datetimeControlOptions = array_merge($dateControlOptions, [
    'type' => DateControl::FORMAT_DATETIME,
    'displayFormat' => 'php:d/m/Y H:i',
    'saveFormat' => 'php:Y-m-d H:i:s',
]);

$initialPreview = [];
$initialPreviewConfig = [];

if ($room->images) {
    foreach ($room->images as $document) {
        $initialPreview[] = $document->getDocumentUrl();
        $initialPreviewConfig[] = [
            'caption' => $document->getDocumentName(),
            'size' => $document->size,
            'url' => Url::to(['delete-images']),
            'key' => $document->id,
        ];
    }
}

SmartPhotoAsset::register($this);
?>

<div class="update-room container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php
            $titlePrefix = '';
            if ($model->room->isExpired()) {
                $titlePrefix = '[' . $model->room->statusLabel() . '] ';
            } ?>

            <h1><?= Html::encode($titlePrefix . $this->title) ?></h1>

            <?php if ($disabledMode): ?>
                <div><?= Yii::t('app', 'The submission of offers is done electronically and by paper.') ?></div>
            <?php endif ?>

            <?php if ($disabledMode && $room->isExpired() && $room->proposalsAllowed) : ?>
                <div>L’offre étant expirée, vous pouvez effectuer une offre de reprise, cependant nous ne garantissons pas de pouvoir la soumettre.</div>
            <?php endif ?>    
        </div>
    </div>

    <div class="row">
        <div class="room-form col-md-6 col-md-offset-3">
            <?php $form = ActiveForm::begin([
                'enableClientValidation' => false,
                'validateOnSubmit' => false,
                'options' => ['enctype' => 'multipart/form-data'],
            ]); ?>

            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Informations générales
                        <div class="pull-right">
                            <a class="btn btn-default btn-sm" data-toggle="collapse" data-target="#general-info" role="button" aria-expanded="true" aria-controls="general-info">V</a>
                        </div>
                    </h3>
                </div>

                <div class="box-body collapse in" id="general-info">
                    <?= $form->field($model, 'id')->textInput(['disabled' => true]) ?>

                    <?= $form->field($room, 'title')->textInput(['maxlength' => 255, 'disabled' => true]) ?>

                    <?= $form->field($model, 'propertyType')->dropDownList(RoomCoownership::getPropertyTypes(), ['prompt' => '', 'disabled' => $disabledMode]) ?>

                    <div class="row address-wrapper" style="margin-right: 0;">
                        <div class="col-md-10">
                            <?= $form->field($model, 'address')->textInput(['maxlength' => 150, 'disabled' => $disabledMode]) ?>
                        </div>

                        <div class="col-md-2" style="margin-top: 24px;">
                            <?php \yii\bootstrap\Modal::begin([
                                'id' => 'address-modal',
                                'header' => '<h4>Localiser</h4>',
                                'footer' => !$disabledMode ? '<span class="btn btn-primary submit">Appliquer</span>' : '',
                                'toggleButton' => ['label' => 'Localiser', 'class' => 'btn btn-default open-modal', 'disabled' => false],
                            ]); ?>
                                <div id="address-placeholder"><?= $model->address ? $model->address : 'Paris' ?></div>
                                <div id="map-canvas" style="width: 100%; height: 400px;"></div>
                            <?php \yii\bootstrap\Modal::end(); ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'zip')->textInput(['maxlength' => 5, 'disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'city')->textInput(['maxlength' => 70, 'disabled' => $disabledMode]) ?>

                    <?= $form->field($model, 'latitude')->textInput(['disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'longitude')->textInput(['disabled' => $disabledMode]) ?>

                    <?= $form->field($model, 'regionID')->dropDownList(ArrayHelper::map(\common\models\Region::getList(), 'id', 'nameWithCode'), ['prompt' => '', 'disabled' => $disabledMode]) ?>

                    <?= $form->field($model, 'missionEndDate')->widget(DateControl::class, $datetimeControlOptions, ['disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'coownershipName')->textInput(['disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'lotsNumber')->textInput(['disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'coownersNumber')->textInput(['disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'mainLotsNumber')->textInput(['disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'secondaryLotsNumber')->textInput(['disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'employeesNumber')->textInput(['disabled' => $disabledMode]) ?>

                    <?php if (!$disabledMode) : ?>
                        <?= $form->field($model, 'lastFinancialYearApprovedAccountsID')->widget(FileInput::class, $fileInputOptions)
                            ->hint($model->lastFinancialYearApprovedAccounts
                                ? \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->lastFinancialYearApprovedAccounts->getDocumentUrl(), ['target' => '_blank'])
                                : '');
                        ?>
                    <?php else : ?>
                        <div class="form-group">
                            <label class="control-label">
                                <?= $model->getAttributeLabel('lastFinancialYearApprovedAccountsID') ?> :
                            </label>
                            &nbsp;&nbsp;<?= \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->lastFinancialYearApprovedAccounts->getDocumentUrl(), ['target' => '_blank']) ?>
                        </div>
                    <?php endif ?>

                    <?= $form->field($model, 'constructionYear')->textInput(['disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'totalFloorsNumber')->textInput(['disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'isElevator')->radioList(ArrayHelper::getYesNoList(), ['itemOptions' => ['disabled' => $disabledMode]]) ?>

                    <?= $form->field($model, 'heatingType')->dropDownList(RoomCoownership::getHeatingTypeList(), ['prompt' => '', 'disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'heatingEnergy')->dropDownList(RoomCoownership::getHeatingEnergyList(), ['prompt' => '', 'disabled' => $disabledMode]) ?>

                    <?= $form->field($model, 'quickDescription')->textarea(['rows' => 4, 'disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'detailedDescription')->textarea(['rows' => 7, 'disabled' => $disabledMode]) ?>
                    <?= $form->field($model, 'keywords')->textarea(['rows' => 4, 'disabled' => $disabledMode]) ?>
                </div>
            </div>

            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title"><?= Yii::t('app', 'Images') ?>
                        <div class="pull-right">
                            <a class="btn btn-default btn-sm" data-toggle="collapse" data-target="#images-info" role="button" aria-expanded="true" aria-controls="images-info">V</a>
                        </div>
                    </h3>
                </div>

                <div class="box-body collapse in" id="images-info">
                    <?php if (!$disabledMode): ?>

                        <?= $form->field($room, 'imageFiles', [
                            'horizontalCssClasses' => ['wrapper' => 'col-sm-12'],
                        ])->widget(FileInput::class, [
                            'options' => [
                                'multiple' => true,
                                'accept' => 'image/*',
                            ],
                            'pluginOptions' => [
                                'previewFileType' => 'any',
                                'showPreview' => true,
                                'showCaption' => false,
                                'showRemove' => true,
                                'showUpload' => true,
                                'showClose' => false,
                                'fileActionSettings' => ['showDrag' => false],
                                'allowedFileExtensions' => ['jpg','jpeg','gif','png'],
                                'browseClass' => 'btn btn-primary btn-block',
                                'uploadUrl' => Url::to(['upload-images', 'id' => $model->id]),
                                'deleteUrl' => Url::to(['delete-images']),
                                'initialPreview' => $initialPreview,
                                'initialPreviewConfig' => $initialPreviewConfig,
                                'overwriteInitial' => false,
                                'initialPreviewAsData' => true,
                            ],
                            'pluginEvents' => [
                                'filebatchselected' => "function(event, files) { $(this).fileinput('upload') }",
                            ],
                        ])->label('') ?>

                    <?php elseif ($room->images) : ?>

                        <div class="image-gallery">
                            <?php foreach ($room->images as $image) : ?>
                                <a class="image-gallery-item" href="<?= $image->getDocumentUrl() ?>" data-group="room-images">
                                    <?= Html::img($image->getDocumentUrl()) ?>
                                </a>
                            <?php endforeach ?>
                        </div>
                    <?php else: ?>
                        - Pas d'images -
                    <?php endif ?>
                </div>
            </div>

            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title"><?= Yii::t('admin', 'Specific assets') ?>
                        <div class="pull-right">
                            <a class="btn btn-default btn-sm" data-toggle="collapse" data-target="#assets-info" role="button" aria-expanded="true" aria-controls="assets-info">V</a>
                        </div>
                    </h3>
                </div>

                <div class="box-body collapse in" id="assets-info">
                    <?= $form->field($model, 'individualAssetsPresence')->radioList(ArrayHelper::getYesNoList(), ['itemOptions' => ['disabled' => true]]) ?>
                    <?= $form->field($model, 'presenceEndDate')->textInput(['disabled' => true, 'value' => $model->presenceEndDate ? Yii::$app->formatter->asDate($model->presenceEndDate, 'php:d/m/Y') : '']) ?>
                    <?= $form->field($model, 'adPosition')->radioList(RoomCoownership::getAdPositionList(), ['itemOptions' => ['disabled' => true]]) ?>
                </div>
            </div>

            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Procédure
                        <div class="pull-right">
                            <a class="btn btn-default btn-sm" data-toggle="collapse" data-target="#procedure-info" role="button" aria-expanded="true" aria-controls="procedure-info">V</a>
                        </div>
                    </h3>
                </div>

                <div class="box-body collapse in" id="procedure-info">
                    <?= $form->field($model, 'procedure')->dropDownList(RoomCoownership::getProcedures(), ['prompt' => '', 'disabled' => true]) ?>
                    <?= $form->field($model, 'procedureContact')->textArea(['disabled' => true]) ?>
                    <?= $form->field($model, 'firstName')->textInput(['disabled' => true]) ?>
                    <?= $form->field($model, 'lastName')->textInput(['disabled' => true]) ?>
                    <?= $form->field($model, 'phone')->textInput(['disabled' => true]) ?>
                    <?= $form->field($model, 'fax')->textInput(['disabled' => true]) ?>
                    <?= $form->field($model, 'phoneMobile')->textInput(['disabled' => true]) ?>
                    <?= $form->field($model, 'email')->textInput(['disabled' => true]) ?>

                    <?= $form->field($room, 'publicationDate')->textInput(['disabled' => true, 'value' => $room->publicationDate->format('d/m/Y')]) ?>
                    <?= $form->field($room, 'archivationDate')->textInput(['disabled' => true, 'value' => $room->archivationDate->format('d/m/Y')]) ?>
                    <?= $form->field($room, 'expirationDate')->textInput(['disabled' => true, 'value' => $room->expirationDate->format('d/m/Y H:i')]) ?>
                    <?= $form->field($model, 'availabilityDate')->textInput(['disabled' => true, 'value' => Yii::$app->formatter->asDate($model->availabilityDate, 'php:d/m/Y')]) ?>
                    <?= $form->field($room, 'createdDate')->textInput(['disabled' => true, 'value' => $room->createdDate->format('d/m/Y H:i')]) ?>

                    <?= $form->field($model, 'homePresence')->radioList(ArrayHelper::getYesNoList(), ['itemOptions' => ['disabled' => true]]) ?>
                    <?= $form->field($model, 'visibility')->radioList(ArrayHelper::getYesNoList(), ['itemOptions' => ['disabled' => true]]) ?>

                    <?= $form->field($model, 'offerAcceptanceCondition')->textArea(['disabled' => true]) ?>
                </div>
            </div>

            <div class="form-group">
                <?php if (!$disabledMode) : ?>
                    <?= Html::submitButton(Yii::t('app', 'Update room'), ['class' => 'btn btn-block submit-btn']) ?>
                <?php elseif (Yii::$app->user->can('makeProposal', ['room' => $room])) : ?>
                    <a class="btn btn-block submit-btn" href="<?= Url::to(['proposal', 'id' => $model->id]) ?>">
                        Faire une proposition
                    </a>
                <?php elseif ($user->hasProposal($room)) : ?>
                    <a class="btn btn-block submit-btn disabled">Proposition envoyée</a>
                <?php endif ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php $this->registerJs("
    new SmartPhoto('.image-gallery-item');
"); ?>

<script type="text/javascript">
    var googleApiKey = "<?= Yii::$app->params['GOOGLE_API_KEY'] ?>";
    var map;
    var marker;
    var addressResults;
    var latitude;
    var longitude;

    function initMap() {
        var options = {
            zoom: 13
        };
        map = new google.maps.Map(document.getElementById("map-canvas"), options);
        var geocoder = new google.maps.Geocoder();

        geocoder.geocode({
            "address": $('#address-placeholder').text()
        }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var center = results[0].geometry.location;
                map.setCenter(center);
                placeMarker(center, map);
            }
        });

        google.maps.event.addListener(map, 'click', function(event) {
            placeMarker(event.latLng, map);

            latitude = event.latLng.lat();
            longitude = event.latLng.lng();

            geocoder.geocode({
                'latLng': event.latLng
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        addressResults = results;
                        $('#address-placeholder').text(results[0].formatted_address);
                    }
                }
            });
        });
    }

    function placeMarker(position, map) {
        if (marker == null) {
            marker = new google.maps.Marker({
                position: position,
                map: map
            });
        } else {
            marker.setPosition(position);
        }
    }

    function addressInit() {
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = "https://maps.googleapis.com/maps/api/js?libraries=geometry,places&ext=.js&language=fr&key=" + googleApiKey;
        document.body.appendChild(script);

        $('#address-modal').on('shown.bs.modal', function () {
            if (map == null) {
                initMap();
            }
        });

        $('#address-modal .btn.submit').on('click', function(e) {
            $('#address-modal').modal('hide');
            applyAddress();
        });
    }

    function applyAddress() {
        console.log(addressResults);
        var city = null, zip = null, address = null;

        for (var i = 0; i < addressResults.length; i++) {
            var result = addressResults[i];

            if (!city && result.types[0] === 'locality') {
                for (var c = 0; c < result.address_components.length; c++) {
                    var component = result.address_components[c];

                    if (component.types[0] === 'locality') {
                        city = component.long_name;
                        break;
                    }
                }
            }

            if (!zip && result.types[0] === 'postal_code') {
                for (var c = 0; c < result.address_components.length; c++) {
                    var component = result.address_components[c];

                    if (component.types[0] === 'postal_code') {
                        zip = component.long_name;
                        break;
                    }
                }
            }

            if (result.types[0] === 'street_address') {
                for (var c = 0; c < result.address_components.length; c++) {
                    var component = result.address_components[c];

                    if (component.types[0] === 'street_number') {
                        address = address ? address + ' ' + component.long_name : component.long_name;
                    } else if (component.types[0] == 'route') {
                        address = address ? address + ' ' + component.long_name : component.long_name;
                    }
                }
            }

            if (city && zip && address) {
                break;
            }
        }

        $('#roomcoownership-address').val(address);
        $('#roomcoownership-zip').val(zip);
        $('#roomcoownership-city').val(city);

        $('#roomcoownership-latitude').val(latitude.toFixed(7));
        $('#roomcoownership-longitude').val(longitude.toFixed(7));
    }

    window.onload = addressInit;
</script>