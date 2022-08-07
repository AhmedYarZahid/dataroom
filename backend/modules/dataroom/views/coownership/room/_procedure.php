<?php

use kartik\datecontrol\DateControl;
use backend\modules\dataroom\models\RoomRealEstate;
use common\helpers\ArrayHelper;

/* @var $room \backend\modules\dataroom\models\Room */

$dateControlOptions = [
    'displayFormat' => 'php:d/m/Y',
    'saveFormat' => 'php:Y-m-d',
    'widgetOptions' => [
        'removeButton' => false,
        'options' => ['placeholder' => ''],
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

$dateControlOptionsHiddenTime = [
    'displayFormat' => 'php:d/m/Y',
    'saveFormat' => 'php:Y-m-d H:i:s',
    'widgetOptions' => [
        'removeButton' => false,
        'options' => ['placeholder' => ''],
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
            //'startDate' => '+0d',
        ]
    ],
];

?>

<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title">Procédure</h3>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-md-10">
            <?= $form->field($model, 'procedure')->dropDownList(\backend\modules\dataroom\models\RoomCoownership::getProcedures(), ['prompt' => '']) ?>
            <?= $form->field($model, 'procedureContact')->textArea() ?>
            <?= $form->field($model, 'firstName')->textInput() ?>
            <?= $form->field($model, 'lastName')->textInput() ?>
            <?= $form->field($model, 'phone')->textInput() ?>
            <?= $form->field($model, 'fax')->textInput() ?>
            <?= $form->field($model, 'phoneMobile')->textInput() ?>
            <?= $form->field($model, 'email')->textInput() ?>

            <?= $form->field($room, 'publicationDate')->widget(DateControl::class, $dateControlOptionsHiddenTime) ?>
            <?= $form->field($room, 'archivationDate')->widget(DateControl::class, $dateControlOptionsHiddenTime) ?>
            <?= $form->field($room, 'expirationDate')->widget(DateControl::class, $datetimeControlOptions) ?>
            <?= $form->field($model, 'availabilityDate')->widget(DateControl::class, $dateControlOptions) ?>
            <?= $form->field($room, 'createdDate')->widget(DateControl::class, $datetimeControlOptions) ?>

            <?= $form->field($model, 'homePresence')->radioList(ArrayHelper::getYesNoList()) ?>
            <?= $form->field($model, 'visibility')->radioList(ArrayHelper::getYesNoList()) ?>

            <?= $form->field($model, 'offerAcceptanceCondition')->textArea() ?>

            <?php if ($room->isExpired()) : ?>
                <?= $form->field($room, 'proposalsAllowed')->checkbox() ?>
            <?php endif ?>

            <?php if (!$room->isNewRecord) : ?>
                <div class="form-group">
                    <label class="control-label col-md-4">URL pour accès à la room</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" value="<?= $model->url ?>" disabled="">
                    </div>
                    <div class="col-md-1">
                        <a href="<?= $model->url ?>" target="_blank" style="line-height: 34px">Ouvrir</a>
                    </div>
                </div>
            <?php endif ?>
            </div>
        </div>
    </div>
</div>