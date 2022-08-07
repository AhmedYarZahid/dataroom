<?php

use yii\bootstrap\Tabs;
use yii\helpers\Html;
use kartik\form\ActiveForm;

$errors = array_merge(array_keys($model->getErrors()), array_keys($room->getErrors()));

$generalFields = [
    'userEmail', 'userName', 'title', 'activity', 'activitysector', 'region', 'website', 'address',
    'zip', 'city', 'desc', 'desc2', 'ca', 'siren', 'codeNaf', 'legalStatus',
    'history', 'concurrence', 'annualTurnover', 'contributors', 'adminID' , 'public',

    'procedureNature', 'designationDate', 'procedureContact',
    'companyContact', 'publicationDate', 'archivationDate', 'hearingDate',
    'createdDate', 'refNumber0', 'refNumber1', 'refNumber2',
];
$procedureFields = [
];

$generalHasErrors = !empty(array_intersect($generalFields, $errors));
$procedureHasErrors = !empty(array_intersect($procedureFields, $errors));

$this->registerJsFile(Yii::$app->request->baseUrl.'/js/dataroom-company.js',['depends' => [\yii\web\JqueryAsset::className()]]);
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
                        'content' => $this->render('_general', compact('form', 'room', 'model')).$this->render('_procedure', compact('form', 'room', 'model')),
                        'active' => true,
                        'headerOptions' => $generalHasErrors ? ['class' => 'error'] : [],
                    ],
                    // [
                    //     'label' => 'Procédure',
                    //     'content' => $this->render('_procedure', compact('form', 'room', 'model')),
                    //     'headerOptions' => $procedureHasErrors ? ['class' => 'error'] : [],
                    // ],
                ],
            ]); ?>

            <div class="form-group">
                <div class="col-sm-12" style="padding-bottom: 50px;">
                <?php $label = $model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Update'); ?>
                <?= Html::submitButton($label, [
                    'class' => 'btn btn-lg btn-primary btn-block',
                    'id' => 'create-room-button'
                    ]) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>