<?php

use yii\grid\GridView;
use yii\widgets\ListView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<div class="container">
    <!--<h1 class="page-header"><?/*= $this->title */?></h1>-->
    <div class="row">

        <?php $form = ActiveForm::begin([
            'enableClientValidation' => false,
            'validateOnSubmit' => false,
            'options' => ['class' => 'form'],
            'method' => 'get',
        ]); ?>

        <div class="col-lg-3">
            <?= $form->field($searchModel, 'region')->widget(\kartik\widgets\Select2::class, [
                'data' => \common\helpers\ArrayHelper::map(\common\models\Department::getList(),
                    'id',
                    function ($model) { return $model->getNameWithCode(); }
                ),
                'options' => [
                    'multiple' => false,
                    'placeholder' => Yii::t('notify', ' - Please select - '),
                    'options' => [ 9999 => ['Selected'=>'selected']]
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => false,
                    'language' => [
                        'noResults' => new \yii\web\JsExpression('function() {
                            return "' . Yii::t('admin', 'No activity sector found.') . '";
                        }'),
                    ],
                ],
                'pluginEvents' => [],
            ]); ?>
        </div>

        <div class="col-lg-3">

            <?= $form->field($searchModel, 'activitysector')->widget(\kartik\widgets\Select2::class, [
                'data' => \common\helpers\ArrayHelper::map(\backend\modules\dataroom\models\RoomCompany::getSectorActivityList(),
                    function ($model) { return $model->getNameWithCode(); }
                ,
                    function ($model) { return $model->getNameWithCode(); }
                ),
                'options' => [
                    'id' => 'kartik-modal',
                    'multiple' => false,
                    'options' => [ 9999 => ['Selected'=>'selected']]
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => Yii::t('notify', ' - Please select - '),
                    'tags' => false,
                    'language' => [
                        'noResults' => new \yii\web\JsExpression('function() {
                            return "' . Yii::t('admin', 'No activity sector found.') . '";
                        }'),
                    ],
                ],
                'pluginEvents' => [],
            ]); ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($searchModel, 'activity') ?>
        </div>

        <div class="col-xs-6 col-lg-2">
            <?php
            echo $form->field($searchModel, 'annualTurnover')->dropdownList(
                $searchModel->getAnnualTurnoverRanges(),
                ['prompt'=>'Sélectionnez']
            );
            ?>
        </div>

        <div class="col-xs-6 col-lg-2">
            <?php
            echo $form->field($searchModel, 'contributors')->dropdownList(
                $searchModel->getContributorsRanges(),
                ['prompt'=>'Sélectionnez']
            );
            ?>
        </div>
        <div class="col-xs-12 col-lg-2">
            <div class="form-group">
                <?= Html::submitButton('Rechercher', ['class' => 'btn btn-primary search-btn']) ?>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <hr>
    <?php ActiveForm::end(); ?>

    <?php
    // dd($dataProvider);
    echo ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_line',
    ]);

    ?>
    <hr>
    <?php
    /*
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label' => 'N° de mandat',
                'format' => 'html',
                'value' => function($model) {
                    return Html::a($model->room->mandateNumber, ['companies/view-room', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'activity',
                'contentOptions' => ['style' => 'width: 500px; max-width: 100%'],
            ],
            'annualTurnover',
            'place',
            [
                'attribute' => 'publicationDate',
                'label' => 'Date de publication',
                'value' => function($model) {
                    return \common\helpers\DateHelper::getFrenchFormatDbDate($model->room->publicationDate);
                }
            ],
        ],
        'pager' => [
            'prevPageLabel' => Yii::t('app', 'Previous'),
            'nextPageLabel' => Yii::t('app', 'Next'),
        ],
    ]);
    */ ?>
</div>


<style type="text/css">
    .room-line {
        /*border: 1px solid red;*/
        border-radius: 8px;
        background: #e9edf3;
    }
</style>