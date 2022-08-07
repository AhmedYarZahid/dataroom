<?php

use yii\helpers\Url;
use common\helpers\FormHelper;
use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use kartik\datecontrol\DateControl;
use yii\bootstrap\Modal;

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

$historyLabels = [
    'Année N', 'Année N-1', 'Année N-2', 'Prévisionnel Année N+1',
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

                <?= $form->field($room, 'mandateNumber', ['options' => ['class' => 'form-group required']])->textInput(['maxlength' => 30 , 'placeholder' => 'Numéro Gemarcur']) ?>

                <?= $form->field($room, 'title')->textInput(['maxlength' => 255 , 'placeholder' => 'Société']) ?>
                <?= $form->field($room, 'public', [ 'options' => [
                    'style' => 'padding-left: 20px;',
                    'class' => 'form-group'
                ]])->checkbox() ?>
                <?= $form->field($model, 'activity')->textInput(['maxlength' => 100]) ?>

                 <?= $form->field($model, 'activitysector')->widget(Select2::classname(), [
                                    'data' => $model->selectSectorActivity(),
                                    'pluginOptions'=> [
                                        'allowClear' => true,
                                        'placeholder' => '',
                                    ],
                ]) ?>
                <?= $form->field($model, 'region')->widget(\kartik\widgets\Select2::class, [
                    'data' => \common\helpers\ArrayHelper::map(\common\models\Department::find()->all(),
                        'id',
                        function ($model) { return $model->getNameWithCode(); }
                    ),
                    'options' => [
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => false,
                        'language' => [
                            'noResults' => new \yii\web\JsExpression('function() {
                                    return "' . Yii::t('admin', 'No users found.') . '";
                                }'),
                        ],
                    ],
                    'pluginEvents' => [],
                ]); ?>
                <?= $form->field($model, 'website')->textInput(['maxlength' => 255]) ?>
                <div class="address-wrapper">
                    <?= $form->field($model, 'address')->textInput(['maxlength' => 255]) ?>

                    <div class="col-md-12">
                        <?php Modal::begin([
                            'id' => 'address-modal',
                            'header' => '<h4>Localiser</h4>',
                            'footer' => '<span class="btn btn-primary submit">Appliquer</span>',
                            'toggleButton' => ['label' => 'Localiser', 'class' => 'btn btn-default open-modal locate'],
                        ]); ?>
                        <div id="address-placeholder"><?= $model->address ? $model->address : 'Paris' ?></div>
                        <div id="map-canvas" style="width: 100%; height: 400px;"></div>
                        <?php Modal::end(); ?>
                    </div>
                    <div class="clearfix"></div>

                    <?= $form->field($model, 'zip')->textInput(['maxlength' => 5]) ?>
                    <?= $form->field($model, 'city')->textInput(['maxlength' => 255]) ?>
                </div>

                <!-- <?php // $form->field($model, 'ca')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'ca')) ?> -->

                <?php if (!$room->isNewRecord): ?>
                    <?= $form->field($room, 'imageFiles')->widget(FileInput::classname(), [
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
                            'uploadUrl' => Url::to(['upload-images', 'id' => $model->id]),
                            'deleteUrl' => Url::to(['delete-images']),
                            'initialPreview' => $initialPreview,
                            'initialPreviewConfig' => $initialPreviewConfig,
                            'overwriteInitial' => false,
                            'initialPreviewAsData' => true,
                        ],
                        'pluginEvents' => [
                            'filebatchselected' => "function(event, files) { $(this).fileinput('upload') }",
                            //'filesorted' => "function(event, params) { console.log(params) }",
                        ],
                    ]) ?>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title">CA et effectif</h3>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-md-10">
                <?php /*
                <?= $form->field($model, 'codeNaf')->widget(Select2::classname(), [
                    'data' => $model->codeNafList(),
                    'pluginOptions'=> [
                        'allowClear' => true,
                        'placeholder' => '',
                    ],
                ]) ?>
                <?= $form->field($model, 'legalStatus')->textInput(['maxlength' => 255]) ?>

                <?= $form->field($model, 'status')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'status')) ?>
                <?= $form->field($model, 'kbis')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'kbis')) ?>
                <?= $form->field($model, 'balanceSheet')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'balanceSheet')) ?>
                <?= $form->field($model, 'incomeStatement')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'incomeStatement')) ?>
                <?= $form->field($model, 'managementBalance')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'managementBalance')) ?>
                <?= $form->field($model, 'taxPackage')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'taxPackage')) ?>

                <table class="table history">
                    <thead>
                        <tr>
                            <th width="180px"></th>
                            <th>Date de début</th>
                            <th>Date de fin</th>
                            <th>Nombre de mois</th>
                            <th>Chiffres d'affaires</th>
                            <th>Marge brute</th>
                            <th>Résultat d'exploitation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 0; $i < 4; $i++) : ?>
                        <tr>
                            <td><?= $historyLabels[$i] ?></td>
                            <td><?= $form->field($model, "history[$i][start_date]")->widget(DateControl::class, $dateControlOptions)->label(false) ?></td>
                            <td><?= $form->field($model, "history[$i][end_date]")->widget(DateControl::class, $dateControlOptions)->label(false) ?></td>
                            <td><?= $form->field($model, "history[$i][months]")->textInput(['maxlength' => 255])->label(false) ?></td>
                            <td><?= $form->field($model, "history[$i][sales]")->textInput(['maxlength' => 255])->label(false) ?></td>
                            <td><?= $form->field($model, "history[$i][margin]")->textInput(['maxlength' => 255])->label(false) ?></td>
                            <td><?= $form->field($model, "history[$i][profit]")->textInput(['maxlength' => 255])->label(false) ?></td>
                        </tr>
                        <?php endfor ?>
                    </tbody>
                </table>

                <?= $form->field($model, 'concurrence')->textArea() ?>
                */ ?>

                <?php /*
                <?= $form->field($model, 'backlog')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'backlog')) ?>
                <?= $form->field($model, 'principalClients')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'principalClients')) ?>
                */ ?>

                <?= $form->field($model, 'annualTurnover')->textInput(['type' => 'number','maxlength' => 50, 'placeholder' => 'K€']) ?>
                <?= $form->field($model, 'contributors')->textInput(['type' => 'number', 'maxlength' => 50, 'placeholder' => 'Effectif salarié (hors dirigeant ou TNS)']) ?>
            </div>
        </div>
    </div>
</div>

<?php /*
<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title">Immobilisations</h3>
    </div>

    <div class="box-body">
        <?= $form->field($model, 'vehicles')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'vehicles')) ?>
        <?= $form->field($model, 'premises')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'premises')) ?>
        <?= $form->field($model, 'baux')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'baux')) ?>
        <?= $form->field($model, 'inventory')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'inventory')) ?>
        <?= $form->field($model, 'assets')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'assets')) ?>
        <?= $form->field($model, 'patents')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'patents')) ?>
    </div>
</div>
*/ ?>

                <?php /*
<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title">Social</h3>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-md-10">

                <?= $form->field($model, 'employmentContract')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'employmentContract')) ?>
                <?= $form->field($model, 'employeesList')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'employeesList')) ?>
                <?= $form->field($model, 'procedureRules')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'procedureRules')) ?>
                <?= $form->field($model, 'rtt')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'rtt')) ?>
                <?= $form->field($model, 'worksCouncilReport')->widget(FileInput::classname(), $fileInputOptions)->hint(FormHelper::downloadLink($model, 'worksCouncilReport')) ?>
            </div>
        </div>
    </div>
</div>
                */ ?>

<script type="text/javascript">
    var googleApiKey = "<?= Yii::$app->params['GOOGLE_API_KEY'] ?>";
    var map;
    var marker;
    var addressResults;
    function initMap() {
        var options = {
            zoom: 15,
        }
        map = new google.maps.Map(document.getElementById("map-canvas"), options);
        var geocoder = new google.maps.Geocoder();

        var addressFilled = $('#roomcompany-address').val()+' '+$('#roomcompany-zip').val()+' '+$('#roomcompany-city').val();

        if($.trim(addressFilled).length)
            $('#address-placeholder').text(addressFilled);

        geocoder.geocode({
            "address": $('#address-placeholder').text()
        }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var center = results[0].geometry.location;
                map.setCenter(center);
                placeMarker(center, map);
                if (results[0]) {
                    addressResults = results;
                    $('#address-placeholder').text(results[0].formatted_address);
                }
            }
        });

        google.maps.event.addListener(map, 'click', function(event) {
            placeMarker(event.latLng, map);

            geocoder.geocode({
                'latLng': event.latLng
            }, function(results, status) {
                    console.log('vince avant OK');
                if (status == google.maps.GeocoderStatus.OK) {
                    console.log('vince');
                    console.log(results);
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

            if (result.types[0] === 'street_address' || result.types[0] === 'route') {
                for (var c = 0; c < result.address_components.length; c++) {
                    var component = result.address_components[c];

                    if (component.types[0] === 'street_number') {
                        address = address ? address + ' ' + component.long_name : component.long_name;
                    }
                    else if (component.types[0] == 'route') {
                        address = address ? address + ' ' + component.long_name : component.long_name;
                    }
                    else if (component.types[0] == 'postal_code') {
                        zip = component.long_name;
                    }
                    else if (component.types[0] == 'locality') {
                        city = component.long_name;
                    }
                }
            }

            if (city && zip && address) {
                break;
            }
        }

        $('#roomcompany-address').val(address);
        $('#roomcompany-zip').val(zip);
        $('#roomcompany-city').val(city);
    }

    window.onload = addressInit;
</script>