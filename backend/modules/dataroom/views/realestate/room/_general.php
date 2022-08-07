<?php

use yii\helpers\Url;
use kartik\widgets\FileInput;
use kartik\datecontrol\DateControl;
use yii\bootstrap\Modal;
use backend\modules\dataroom\models\RoomRealEstate;
use common\helpers\ArrayHelper;

/* @var $room \backend\modules\dataroom\models\Room */

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
        'options' => ['placeholder' => ''],
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
        ]
    ],
];

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

?>

<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title">Informations générales</h3>
    </div>
    
    <div class="box-body">
        <div class="row">
            <div class="col-md-10">

                <?= $this->render('@app/modules/dataroom/views/companies/room/_new_room_begin_form', [
                    'form' => $form,
                    'room' => $room,
                    'model' => $model,
                ]) ?>

                <?= $form->field($room, 'mandateNumber', ['options' => ['class' => 'form-group required']])->textInput(['maxlength' => 30]) ?>

                <?= $form->field($room, 'title')->textInput(['maxlength' => 255]) ?>

                <?= $form->field($model, 'mission')->textInput(['maxlength' => 250]) ?>
                <?= $form->field($model, 'marketing')->radioList(RoomRealEstate::getMarketingList()) ?>
                <?= $form->field($model, 'status')->radioList(RoomRealEstate::getStatuses()) ?>

                <?= $form->field($model, 'propertyType')->dropDownList(RoomRealEstate::getPropertyTypes(), ['prompt' => '']) ?>
                <?= $form->field($model, 'propertySubType')->dropDownList(RoomRealEstate::getPropertySubTypes(), ['prompt' => '']) ?>

                <?= $form->field($model, 'libAd')->textInput() ?>

                <div class="address-wrapper">
                    <?= $form->field($model, 'address')->textInput(['maxlength' => 150]) ?>

                    <div class="col-md-12">
                        <?php Modal::begin([
                            'id' => 'address-modal',
                            'header' => '<h4>Localiser</h4>',
                            'footer' => '<span class="btn btn-primary submit">Appliquer</span>',
                            'toggleButton' => ['label' => 'Localiser', 'class' => 'btn btn-default open-modal'],
                        ]); ?>
                        <div id="address-placeholder"><?= $model->address ? $model->address : 'Paris' ?></div>
                        <div id="map-canvas" style="width: 100%; height: 400px;"></div>
                        <?php Modal::end(); ?>
                    </div>
                    <div class="clearfix"></div>

                    <?= $form->field($model, 'zip')->textInput(['maxlength' => 5]) ?>
                    <?= $form->field($model, 'city')->textInput(['maxlength' => 70]) ?>

                    <?= $form->field($model, 'latitude')->textInput() ?>
                    <?= $form->field($model, 'longitude')->textInput() ?>

                    <?= $form->field($model, 'countryID')->dropDownList(ArrayHelper::map(\common\models\Country::getList(), 'id', 'name'), ['prompt' => '']) ?>
                    <?= $form->field($model, 'regionID')->dropDownList(ArrayHelper::map(\common\models\Region::getList(), 'id', 'nameWithCode'), ['prompt' => '']) ?>
                </div>

                <?= $form->field($model, 'ca')->widget(FileInput::classname(), $fileInputOptions)->hint(\common\helpers\FormHelper::downloadLink($model, 'ca')) ?>

                <?= $form->field($model, 'constructionYear')->textInput() ?>
                <?= $form->field($model, 'totalFloorsNumber')->textInput() ?>
                <?= $form->field($model, 'floorNumber')->textInput() ?>

                <?= $form->field($model, 'area')->textInput() ?>

                <?= $form->field($model, 'isDuplex')->radioList(ArrayHelper::getYesNoList()) ?>
                <?= $form->field($model, 'isElevator')->radioList(ArrayHelper::getYesNoList()) ?>

                <?= $form->field($model, 'roomsNumber')->textInput() ?>
                <?= $form->field($model, 'bedroomsNumber')->textInput() ?>
                <?= $form->field($model, 'bathroomsNumber')->textInput() ?>
                <?= $form->field($model, 'showerRoomsNumber')->textInput() ?>
                <?= $form->field($model, 'kitchensNumber')->textInput() ?>
                <?= $form->field($model, 'toiletsNumber')->textInput() ?>
                <?= $form->field($model, 'isSeparateToilet')->radioList(ArrayHelper::getYesNoList()) ?>
                <?= $form->field($model, 'separateToiletsNumber')->textInput() ?>

                <?= $form->field($model, 'heatingType')->dropDownList(RoomRealEstate::getHeatingTypeList(), ['prompt' => '']) ?>
                <?= $form->field($model, 'heatingEnergy')->dropDownList(RoomRealEstate::getHeatingEnergyList(), ['prompt' => '']) ?>

                <?= $form->field($model, 'facilityIDs')->widget(\kartik\widgets\Select2::class, [
                    'data' => ArrayHelper::map(\backend\modules\dataroom\models\RoomFacility::getList(), 'id', 'name'),
                    'options' => [
                        'multiple' => true,
                        'placeholder' => '', //Yii::t('admin', 'Start typing facility name'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => false,
                        'language' => [
                            'noResults' => new \yii\web\JsExpression('function() {
                                return "' . Yii::t('admin', 'No facilities found.') . '";
                            }'),
                        ],
                    ],
                    'pluginEvents' => [],
                ]); ?>

                <?= $form->field($model, 'cupboardIDs')->widget(\kartik\widgets\Select2::class, [
                    'data' => ArrayHelper::map(\backend\modules\dataroom\models\RoomCupboard::getList(), 'id', 'name'),
                    'options' => [
                        'multiple' => true,
                        'placeholder' => '', //Yii::t('admin', 'Start typing cupboard name'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => false,
                        'language' => [
                            'noResults' => new \yii\web\JsExpression('function() {
                                return "' . Yii::t('admin', 'No cupboards found.') . '";
                            }'),
                        ],
                    ],
                    'pluginEvents' => [],
                ]); ?>

                <?= $form->field($model, 'roomTypeIDs')->widget(\kartik\widgets\Select2::class, [
                    'data' => ArrayHelper::map(\backend\modules\dataroom\models\RoomType::getList(), 'id', 'name'),
                    'options' => [
                        'multiple' => true,
                        'placeholder' => '', //Yii::t('admin', 'Start typing room type name'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => false,
                        'language' => [
                            'noResults' => new \yii\web\JsExpression('function() {
                                return "' . Yii::t('admin', 'No room types found.') . '";
                            }'),
                        ],
                    ],
                    'pluginEvents' => [],
                ]); ?>

                <?= $form->field($model, 'orientationIDs')->widget(\kartik\widgets\Select2::class, [
                    'data' => ArrayHelper::map(\backend\modules\dataroom\models\RoomOrientation::getList(), 'id', 'name'),
                    'options' => [
                        'multiple' => true,
                        'placeholder' => '', //Yii::t('admin', 'Start typing orientation name'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => false,
                        'language' => [
                            'noResults' => new \yii\web\JsExpression('function() {
                                return "' . Yii::t('admin', 'No orientations found.') . '";
                            }'),
                        ],
                    ],
                    'pluginEvents' => [],
                ]); ?>


                <?= $form->field($model, 'proximity')->textInput() ?>

                <?= $form->field($model, 'quickDescription')->textarea(['rows' => 4]) ?>
                <?= $form->field($model, 'detailedDescription')->textarea(['rows' => 7]) ?>
                <?= $form->field($model, 'keywords')->textarea(['rows' => 4]) ?>
            </div>
        </div>
    </div>
</div>

<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title"><?= Yii::t('app', 'Financial') ?></h3>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-md-10">
                <?= $form->field($model, 'sellingPrice')->textInput() ?>

                <?= $form->field($model, 'totalPrice')->textInput() ?>
                <?= $form->field($model, 'totalPriceFrequency')->dropDownList(RoomRealEstate::getPaymentFrequencyList(), ['prompt' => '']) ?>

                <?= $form->field($model, 'charges')->textInput() ?>
                <?= $form->field($model, 'chargesFrequency')->dropDownList(RoomRealEstate::getPaymentFrequencyList(), ['prompt' => '']) ?>

                <?= $form->field($model, 'currency')->dropDownList(RoomRealEstate::getCurrencyList(), ['prompt' => '']) ?>

                <?= $form->field($model, 'propertyTax')->textInput() ?>
                <?= $form->field($model, 'housingTax')->textInput() ?>

                <?= $form->field($model, 'condominiumLotsNumber')->textInput() ?>
                <?= $form->field($model, 'adLotNumber')->textInput() ?>
            </div>
        </div>
    </div>
</div>

<?php if (!$room->isNewRecord) : ?>
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title"><?= Yii::t('app', 'Images') ?></h3>
        </div>

        <div class="box-body">
            <div class="row">
                <div class="col-md-10">
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
                            'showRemove' => false,
                            'showUpload' => false,
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
                </div>
            </div>
        </div>
    </div>
<?php endif ?>

<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title"><?= Yii::t('admin', 'Specific assets') ?></h3>
    </div>
    
    <div class="box-body">
        <div class="row">
            <div class="col-md-10">
                <?= $form->field($model, 'individualAssetsPresence')->radioList(ArrayHelper::getYesNoList()) ?>
                <?= $form->field($model, 'presenceEndDate')->widget(DateControl::class, $dateControlOptions) ?>
                <?= $form->field($model, 'adPosition')->radioList(RoomRealEstate::getAdPositionList()) ?>
            </div>
        </div>
    </div>
</div>

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

        $('#roomrealestate-address').val(address);
        $('#roomrealestate-zip').val(zip);
        $('#roomrealestate-city').val(city);

        $('#roomrealestate-latitude').val(latitude.toFixed(7));
        $('#roomrealestate-longitude').val(longitude.toFixed(7));
    }

    window.onload = addressInit;
</script>