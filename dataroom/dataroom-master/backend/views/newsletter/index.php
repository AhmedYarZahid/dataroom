<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use \kartik\form\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel common\models\NewsletterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Newsletter Subscribers');
$this->params['breadcrumbs'][] = '<i class="fa fa-newspaper-o"></i> ' . $this->title;
?>
<div class="newsletter-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Add Subscription'), ['create'], ['class' => 'btn btn-success']) ?>&nbsp;&nbsp;<?= Yii::t('app', 'or') ?>&nbsp;

        <?php \yii\bootstrap\Modal::begin([
            'header' => '<b>' . Yii::t('admin', 'Please choose .csv file that contains emails list') . '</b>',
            'toggleButton' => [
                'label' => Yii::t('admin', 'Import emails via .csv'),
                'class' => 'btn btn-warning'
            ],
        ]);

        ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data']
        ]);
        echo \kartik\widgets\FileInput::widget([
            'name' => 'emailsList',
            'options' => ['accept' => '*/csv'],
            'pluginOptions' => [
                'allowedFileExtensions' => ['csv'],
                'showPreview' => true,
                'browseLabel' =>  Yii::t('admin', 'Select CSV')
            ]
        ]);
        ActiveForm::end();

        \yii\bootstrap\Modal::end(); ?>
    </p>

    <?php
    use kartik\export\ExportMenu;
    $gridColumns = [
        [
            'attribute' => 'id',
            'options' => ['style' => 'width:100px;'],
            'hAlign' => 'center',
            'vAlign' => 'middle',
        ],
        [
            'attribute' => 'email',
            'format' => 'email',
            'hAlign' => 'center',
            'vAlign' => 'middle',
        ],
        [
            'attribute' => 'firstName',
            'hAlign' => 'center',
            'vAlign' => 'middle',
        ],
        [
            'attribute' => 'lastName',
            'hAlign' => 'center',
            'vAlign' => 'middle',
        ],
        [
            'attribute' => 'profession',
            'filter' => $searchModel->professionList(),
            'value' => function($model) {
                return $model->professionCaption();
            }
        ],
        [
            'attribute' => 'createdDate',
            'format' => ['date', 'php:d/m/Y'],
            'filter' => false,
            'hAlign' => 'center',
            'vAlign' => 'middle'
        ],

        [
            'class' => 'kartik\grid\ActionColumn',
            'hAlign' => 'center',
            'vAlign' => 'middle',
            //'options' => ['style' => 'width: 65px;']
        ],
    ];

    // Renders an export dropdown menu
    $fullExportMenu = ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'target' => ExportMenu::TARGET_SELF,
        'showConfirmAlert' => false,
        'fontAwesome' => true,
        'pjaxContainerId' => 'pjax-newsletter',

        'noExportColumns' => [0, 6],
        'selectedColumns' => [1, 2, 3, 4, 5],

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
        'filename' => 'newsletter-subscribers-export',
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
    ]);
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,

        'pjaxSettings' => ['options' => ['id' => 'pjax-newsletter']],

        'panel' => [
            'type' => 'default',
            'heading' => ''
        ],

        'toolbar' => [
            $fullExportMenu,
        ],

        'columns' => $gridColumns,
    ]); ?>

</div>
