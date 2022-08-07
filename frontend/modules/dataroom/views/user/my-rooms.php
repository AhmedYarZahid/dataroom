<?php
    
use kartik\grid\GridView;
use yii\helpers\Html;
use backend\modules\dataroom\Module as DataroomModule;

if ($section == DataroomModule::SECTION_COMPANIES) {
    $columns = [
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
    ];
} elseif ($section == DataroomModule::SECTION_REAL_ESTATE) {
    $columns = [
        [
            'label' => 'N° de mandat',
            'format' => 'html',
            'value' => function($model) {
                return Html::a($model->room->mandateNumber, ['real-estate/view-room', 'id' => $model->id]);
            }
        ],
        [
            'attribute' => 'libAd',
        ],
        [
            'attribute' => 'propertyType',
            'value' => function($model) {
                return \backend\modules\dataroom\models\RoomRealEstate::getPropertyTypeCaption($model->propertyType);
            }
        ],
        [
            'attribute' => 'zip',
        ],
    ];
} elseif ($section == DataroomModule::SECTION_COOWNERSHIP) {
    $columns = [
        [
            'label' => 'N° de mandat',
            'format' => 'html',
            'value' => function($model) {
                return Html::a($model->room->mandateNumber, ['coownership/view-room', 'id' => $model->id]);
            }
        ],
        [
            'attribute' => 'propertyType',
            'value' => function($model) {
                return \backend\modules\dataroom\models\RoomCoownership::getPropertyTypeCaption($model->propertyType);
            }
        ],
        [
            'attribute' => 'lotsNumber',
        ],
        [
            'attribute' => 'zip',
        ],
    ];
} elseif ($section == DataroomModule::SECTION_CV) {
    $columns = [
        [
            'label' => 'N° de mandat',
            'format' => 'html',
            'value' => function($model) {
                return Html::a($model->room->mandateNumber, ['cv/view-room', 'id' => $model->id]);
            }
        ],
        [
            'attribute' => 'functionID',
            'value' => function($model) {
                return $model->getFunctionName();
            }
        ],
        [
            'attribute' => 'candidateProfile',
            'format' => 'ntext'
        ],
        [
            'attribute' => 'activityDomainID',
            'value' => function($model) {
                return $model->getActivityDomainName();
            }
        ],
        [
            'attribute' => 'regionID',
            'value' => function($model) {
                return $model->getRegionName();
            }
        ],
    ];
}


if ($user->isBuyer()) {
    $columns[] = [
        'label' => Yii::t('app', 'Request access status'),
        'value' => function($model) {
            return \backend\modules\dataroom\models\RoomAccessRequest::getStatusCaption($model->room->currentUserAccessRequest->status);
        }
    ];
}

$columns[] = [
    'attribute' => 'roomStatus',
    'label' => Yii::t('admin', 'Room status'),
    'value' => function($model) {
        return $model->room->statusLabel();
    }
];

/*$columns[] =
[
    'class' => '\kartik\grid\ActionColumn',
    'template' => '{update}',
    'buttons' => [
        'update' => function ($url, $model, $key) {
            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/dataroom/companies/update-room', 'id' => $model->id], ['title' => Yii::t('app', 'Update'), 'data' => [
                'pjax' => 0,
            ]]);
        },
    ],
    'visibleButtons' => [
        'update' => function ($model, $key, $index) {
            return Yii::$app->user->can('updateRoom', ['room' => $model->room]);
        },
    ],
    'visible' => Yii::$app->user->can('manager') || Yii::$app->user->can('admin')
];*/
?>

<div class="my-rooms container">
    <?php if ($dataProvider) : ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => $columns,
            'pager' => [
                'prevPageLabel' => Yii::t('app', 'Previous'),
                'nextPageLabel' => Yii::t('app', 'Next'),
            ],
            'pjax' => false,
        ]) ?>
    <?php endif ?>
</div>