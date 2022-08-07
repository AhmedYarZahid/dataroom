<?php

use yii2tech\csvgrid\CsvGrid;
use kartik\datecontrol\DateControl;
use common\helpers\DateHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\mailing\models\MailingCampaignSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$exporter = new CsvGrid([
    'dataProvider' => $mailingDataProvider,
    'csvFileConfig' => [
        'cellDelimiter' => ';',
    ],
    'columns' => [
        'uniqueName',
        [
            'attribute' => 'listID',
            'value' => function ($model) {
                return $model->list->name;
            },
        ],
        'subject',
        [
            'attribute' => 'status',
            'value' => function ($model) {
                return $model->getStatusCaption();
            },
        ],
        [
            'label' => 'Envoyés',
            'value' => function ($model) use ($mailingStats) {
                if (isset($mailingStats[$model->uniqueName])) {
                    return $mailingStats[$model->uniqueName]['DeliveredCount'];
                }

                return '';
            },
        ],
        [
            'label' => 'Non envoyés',
            'value' => function ($model) use ($mailingStats) {
                if (isset($mailingStats[$model->uniqueName])) {
                    return $mailingStats[$model->uniqueName]['ProcessedCount'] - $mailingStats[$model->uniqueName]['DeliveredCount'];
                }

                return '';
            },
        ],
        [
            'label' => 'Ouverts',
            'value' => function ($model) use ($mailingStats) {
                if (isset($mailingStats[$model->uniqueName])) {
                    return $mailingStats[$model->uniqueName]['OpenedCount'];
                }

                return '';
            },
        ],
        [
            'attribute' => 'sentDate',
            'value' => function($model) {
                return DateHelper::getFrenchFormatDbDate($model->sentDate, true);
            }
        ],
    ],
]);

$exporter->export()->send('room-' . $mailingSearchModel->roomID . '-mailing-export.csv');
