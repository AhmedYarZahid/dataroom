<?php

use yii\helpers\Html;
use kartik\datecontrol\DateControl;
use kartik\widgets\Select2;

$dateControlOptions = [
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

$datetimeControlOptions = array_merge($dateControlOptions, [
    'type' => DateControl::FORMAT_DATETIME,
    'displayFormat' => 'php:d/m/Y H:i',
]);

?>

<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title">Procédure</h3>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-md-10">


                 <?= $form->field($model, 'procedureNature')->widget(Select2::classname(), [
                                    'data' => $model->selectProcedureNature(),
                                    'pluginOptions'=> [
                                        'allowClear' => true,
                                        'placeholder' => '',
                                    ],
                ]) ?>
                <?= $form->field($model, 'designationDate')->widget(DateControl::class, $dateControlOptions) ?>
                <?= $form->field($model, 'procedureContact')->textInput([
                    'maxlength' => 255,
                    'placeholder'=> 'x.xxx@ajassocies.fr'
                ])->hint('l\'adresse mail doit se terminer par @ajassocies.fr') ?>
                <?= $form->field($room, 'publicationDate')->widget(DateControl::class, $dateControlOptions) ?>
                <?= $form->field($room, 'archivationDate')->widget(DateControl::class, $dateControlOptions) ?>
                <?= $form->field($model, 'hearingDate')->widget(DateControl::class, $dateControlOptions) ?>

                <?= $form->field($model, 'refNumber0')->widget(DateControl::class, $datetimeControlOptions) ?>
                <?= $form->field($model, 'refNumber1')->widget(DateControl::class, $datetimeControlOptions) ?>
                <?= $form->field($model, 'refNumber2')->widget(DateControl::class, $datetimeControlOptions) ?>
                <?php $datetimeControlOptions['disabled'] = true; ?>
                <?= $form->field($room, 'createdDate')->widget(DateControl::class, $datetimeControlOptions) ?>
                <?php unset($datetimeControlOptions['disabled']); ?>
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

                    <div class="form-group">
                        <div class="col-md-4 col-md-offset-4">
                            <img src="<?= $model->qrCodeSrc ?>">
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>