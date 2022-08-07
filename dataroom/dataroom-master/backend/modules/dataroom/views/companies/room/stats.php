<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\datecontrol\DateControl;
use common\helpers\DateHelper;
use kartik\export\ExportMenu;

$this->title = Yii::t('admin', 'Stats');
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-table"></i> AJArepreneurs', 'url' => ['index']];
$this->params['breadcrumbs'][] = '<i class="fa fa-th"></i> ' . $this->title;

$exportConfig = [
    'target' => ExportMenu::TARGET_SELF,
    'showConfirmAlert' => false,
    'fontAwesome' => true,

    //'noExportColumns' => [0, 6],
    //'selectedColumns' => [1, 2, 3, 4, 5],

    'columnBatchToggleSettings' => [
        'show' => false
    ],

    'exportConfig' => [
        ExportMenu::FORMAT_EXCEL_X => [
            'label' => Yii::t('admin', 'Excel')
        ],
        ExportMenu::FORMAT_PDF => false,
        ExportMenu::FORMAT_HTML => false,
        ExportMenu::FORMAT_EXCEL => false
    ],
    'onInitWriter' => function ($writer, $grid) {
        if ($writer instanceof PHPExcel_Writer_CSV) {
            $writer->setDelimiter(';');
        }
    },

    'dropdownOptions' => [
        'label' => Yii::t('admin', 'Export'),
        'class' => 'btn btn-info',
        'itemsBefore' => [
            '<li class="dropdown-header">' . Yii::t('admin', 'Export All Data') . '</li>',
        ],
    ],
];

$accessRequestColumns = [
    [
        'attribute' => 'userEmail',
        'label' => Yii::t('admin', 'Email'),
        'value' => function($model) {
            return $model->user->email;
        },
    ],
    [
        'attribute' => 'userLastName',
        'label' => Yii::t('admin', 'Last Name'),
        'value' => function($model) {
            return $model->user->lastName;
        },
    ],
    [
        'attribute' => 'userFirstName',
        'label' => Yii::t('admin', 'First Name'),
        'value' => function($model) {
            return $model->user->firstName;
        },
    ],
    [
        'attribute' => 'userCompany',
        'label' => Yii::t('admin', 'Company Name'),
        'value' => function($model) {
            return $model->user->companyName;
        },
    ],
    [
        'attribute' => 'createdDate',
        'label' => Yii::t('admin', 'Date'),
        'filterType' => DateControl::class,
        'value' => function($model) {
            return DateHelper::getFrenchFormatDbDate($model->createdDate, true);
        }
    ],
];

$roomHistoryColumns = [
    [
        'attribute' => 'userEmail',
        'label' => Yii::t('admin', 'Email'),
        'value' => function($model) {
            return $model->user ? $model->user->email : 'IP : ' . long2ip($model->ip);
        },
    ],
    [
        'attribute' => 'userLastName',
        'label' => Yii::t('admin', 'Last Name'),
        'value' => function($model) {
            return $model->user ? $model->user->lastName : '';
        },
    ],
    [
        'attribute' => 'userFirstName',
        'label' => Yii::t('admin', 'First Name'),
        'value' => function($model) {
            return $model->user ? $model->user->firstName : '';
        },
    ],
    [
        'attribute' => 'userCompany',
        'label' => Yii::t('admin', 'Company Name'),
        'value' => function($model) {
            return $model->user ? $model->user->companyName : '';
        },
    ],
    [
        'attribute' => 'status',
        'label' => Yii::t('admin', 'Status'),
        'value' => function($model) {
            if ($model->user) {
                return $model->hasFullAccess ? 'Accès complet' : 'Accès restreint';
            }

            return 'Non connecté';
        },
    ],
    [
        'attribute' => 'createdDate',
        'label' => Yii::t('admin', 'Date'),
        'filterType' => DateControl::class,
        'value' => function($model) {
            return DateHelper::getFrenchFormatDbDate($model->createdDate, true);
        }
    ],
];

$docHistoryColumns = [
    [
        'label' => Yii::t('admin', 'Folder'),
        'value' => function($model) use ($docHistory) {
            return $model->getFolderName();
        },
    ],
    [
        'attribute' => 'documentName',
        'label' => Yii::t('admin', 'Name'),
        'value' => function($model) {
            return $model->document ? $model->document->getDocumentName() : 'Tous les documents';
        },
    ],
    [
        'attribute' => 'userEmail',
        'label' => Yii::t('admin', 'Email'),
        'value' => function($model) {
            return $model->user ? $model->user->email : 'IP : ' . long2ip($model->ip);
        },
    ],
    [
        'attribute' => 'userLastName',
        'label' => Yii::t('admin', 'Last Name'),
        'value' => function($model) {
            return $model->user ? $model->user->lastName : '';
        },
    ],
    [
        'attribute' => 'userFirstName',
        'label' => Yii::t('admin', 'First Name'),
        'value' => function($model) {
            return $model->user ? $model->user->firstName : '';
        },
    ],
    [
        'attribute' => 'userCompany',
        'label' => Yii::t('admin', 'Company Name'),
        'value' => function($model) {
            return $model->user ? $model->user->companyName : '';
        },
    ],
    [
        'attribute' => 'createdDate',
        'label' => Yii::t('admin', 'Date'),
        'filterType' => DateControl::class,
        'value' => function($model) {
            return DateHelper::getFrenchFormatDbDate($model->createdDate, true);
        }
    ],
    [
        'label' => Yii::t('admin', 'Total downloads'),
        'value' => function($model) {
            return $model->document ? $model->document->downloads : '';
        },
    ],
];

?>

<div class="room-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <br>
    
    <h4>Nombre de propositions de reprise : <?= $room->getProposals()->count() ?></h4>

    <?= GridView::widget([
        'dataProvider' => $accessRequestSearchProvider,
        'filterModel' => $accessRequestSearch,
        'columns' => $accessRequestColumns,
        'panel' => [
            'type' => 'default',
            'heading' => "Demandes d'accès des repreneurs",
            'footer' => false,
            'after' => false,
        ],
        'toolbar' => [
            ExportMenu::widget($exportConfig + [
                'dataProvider' => $accessRequestSearchProvider,
                'columns' => $accessRequestColumns,
                'pjaxContainerId' => 'pjax-access-request',
                'filename' => "[Room {$room->id}] Demandes d'accès des repreneurs",
            ]),
        ],
        'pjaxSettings' => ['options' => ['id' => 'pjax-access-request']],
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $roomHistoryProvider,
        'filterModel' => $roomHistory,
        'columns' => $roomHistoryColumns,
        'panel' => [
            'type' => 'default',
            'heading' => "Connexions à la room par les utilisateurs",
            'footer' => false,
            'after' => false,
        ],
        'toolbar' => [
            ExportMenu::widget($exportConfig + [
                'dataProvider' => $roomHistoryProvider,
                'columns' => $roomHistoryColumns,
                'pjaxContainerId' => 'pjax-room-history',
                'filename' => "[Room {$room->id}] Connexions à la room par les utilisateurs",
            ]),
        ],
        'pjaxSettings' => ['options' => ['id' => 'pjax-room-history']],
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $docHistoryProvider,
        'filterModel' => $docHistory,
        'columns' => $docHistoryColumns,
        'panel' => [
            'type' => 'default',
            'heading' => "Consulations/téléchargemens des documents",
            'footer' => false,
            'after' => false,
        ],
        'toolbar' => [
            ExportMenu::widget($exportConfig + [
                'dataProvider' => $docHistoryProvider,
                'columns' => $docHistoryColumns,
                'pjaxContainerId' => 'pjax-download-history',
                'filename' => "[Room {$room->id}] Consulations/téléchargemens des documents",
            ]),
        ],
        'pjaxSettings' => ['options' => ['id' => 'pjax-download-history']],
    ]); ?>

    <div>
        <div style="float: left;">
            <h2><?= Yii::t('admin', 'Mailing Campaigns') ?></h2>
        </div>

        <div style="padding-top: 27px;">
            <?= Html::a('<i class="glyphicon glyphicon-export"></i> ' . Yii::t('app', 'Export'),
                /*\yii\helpers\Url::current(['export' => 1])*/ 'javascript:void(0);', [
                'class' => 'btn btn-info pull-right',
                'onclick' => 'js: var separator = (window.location.href.indexOf("?") === -1) ? "?" : "&"; location.href = window.location.href + separator + "export=1"',
            ]); ?>
        </div>
    </div>

    <div class="clearfix"></div>

    <?= $this->render('@app/modules/mailing/views/campaign/_list', [
        'dataProvider' => $mailingDataProvider,
        'searchModel' => $mailingSearchModel,
        'stats' => $mailingStats,
        'forStats' => true
    ]) ?>
</div>