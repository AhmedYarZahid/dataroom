<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\datecontrol\DateControl;
use common\helpers\DateHelper;

$this->title = Yii::t('admin', 'Access requests');
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-table"></i>AJAimmo', 'url' => ['realestate/room/index']];
$this->params['breadcrumbs'][] = '<i class="fa fa-table"></i> ' . $this->title;
?>

<div class="room-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'accessRequestID',
            [
                'attribute' => 'roomTitle',
                'label' => Yii::t('admin', 'Room'),
                'value' => function($model) {
                    return $model->accessRequest->room->title;
                }
            ],
            [
                'attribute' => 'userEmail',
                'label' => Yii::t('admin', 'Buyer email'),
                'value' => function($model) {
                    return $model->accessRequest->user->email;
                }
            ],
            [
                'attribute' => 'createdDate',
                'label' => Yii::t('admin', 'Date'),
                'filterType' => DateControl::class,
                'value' => function($model) {
                    return DateHelper::getFrenchFormatDbDate($model->accessRequest->createdDate, true);
                }
            ],
            /*[
                'class' => '\kartik\grid\BooleanColumn',
                'attribute' => 'isValidated',
                'label' => Yii::t('admin', 'Validated'),
                'value' => function ($model, $key, $index, $column) {
                    return $model->accessRequest->validatedBy ? 1 : 0;
                },
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'trueLabel' => Yii::t('admin', 'Yes'),
                'falseLabel' => Yii::t('admin', 'No'),
            ],*/
            [
                'attribute' => 'status',
                'label' => Yii::t('admin', 'Status'),
                'filter' => \backend\modules\dataroom\models\RoomAccessRequest::getStatuses(),
                'value' => function($model) {
                    return \backend\modules\dataroom\models\RoomAccessRequest::getStatusCaption($model->accessRequest->status);
                },
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'attribute' => 'validatedBy',
                'label' => Yii::t('admin', 'Validated By'),
                'value' => function($model) {
                    return $model->accessRequest->validatedBy ? $model->accessRequest->admin->email : null;
                }
            ],
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function ($model, $key, $index, $column) {
                    return $this->render('_expanded', ['model' => $model]);
                },
                'expandOneOnly' => true,
                'detailAnimationDuration' => 0,
            ],
            [
                'class' => '\kartik\grid\ActionColumn',
                'template' => '{validate}&nbsp;&nbsp;{refuse}',
                'buttons' => [
                    'validate' => function ($url, $model, $key) {
                        return $model->accessRequest->status == \backend\modules\dataroom\models\RoomAccessRequest::STATUS_WAITING ? Html::a('<span class="glyphicon glyphicon-check"></span>', $url, ['title' => Yii::t('admin', 'Validate'), 'data' => [
                            'confirm' => Yii::t('admin', 'Are you sure you want to validate this request?'),
                            'method' => 'post',
                        ]]) : '';
                    },
                    'refuse' => function ($url, $model, $key) {
                        return $model->accessRequest->status == \backend\modules\dataroom\models\RoomAccessRequest::STATUS_WAITING ? Html::a('<span class="glyphicon glyphicon-remove-circle"></span>', $url, ['title' => Yii::t('admin', 'Refuse'), 'data' => [
                            'confirm' => Yii::t('admin', 'Are you sure you want to refuse this request?'),
                            'method' => 'post',
                        ]]) : '';
                    },
                ],
                'options' => ['style' => 'width: 65px;']
            ],
        ],
    ]); ?>
</div>