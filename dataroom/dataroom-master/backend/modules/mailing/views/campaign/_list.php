<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\datecontrol\DateControl;
use common\helpers\DateHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\mailing\models\MailingCampaignSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$forStats = !empty($forStats);
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'uniqueName',
        [
            'attribute' => 'listID',
            'filter' => $searchModel->listOptions(),
            'value' => function ($model) {
                return $model->list->name;
            },
        ],
        'subject',
        [
            'attribute' => 'status',
            'filter' => $searchModel->statusOptions(),
            'value' => function ($model) {
                return $model->getStatusCaption();
            },
        ],
        [
            'label' => 'Envoyés',
            'value' => function ($model) use ($stats) {
                if (isset($stats[$model->uniqueName])) {
                    return $stats[$model->uniqueName]['DeliveredCount'];
                }

                return '';
            },
        ],
        [
            'label' => 'Non envoyés',
            'value' => function ($model) use ($stats) {
                if (isset($stats[$model->uniqueName])) {
                    return $stats[$model->uniqueName]['ProcessedCount'] - $stats[$model->uniqueName]['DeliveredCount'];
                }

                return '';
            },
        ],
        [
            'label' => 'Ouverts',
            'value' => function ($model) use ($stats) {
                if (isset($stats[$model->uniqueName])) {
                    return $stats[$model->uniqueName]['OpenedCount'];
                }

                return '';
            },
        ],
        [
            'attribute' => 'sentDate',
            'filterType' => DateControl::class,
            'value' => function($model) {
                return DateHelper::getFrenchFormatDbDate($model->sentDate, true);
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
            'template' => '{update} {delete}',
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    return $model->status != $model::STATUS_SENT ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => Yii::t('admin', 'Update'), 'data-pjax' => 0]) : '';
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => Yii::t('admin', 'Delete'), 'data' => [
                        'confirm' => Yii::t('admin', 'Are you sure you want to delete this user?'),
                        'method' => 'post',
                    ]]);
                },
            ],
            //'deleteOptions' => ['message' => 'Custom message'],
            'options' => ['style' => 'width: 100px;'],
            'visible' => !$forStats
        ],
    ],
]); ?>