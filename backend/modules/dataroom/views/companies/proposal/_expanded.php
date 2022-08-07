<?php 

use yii\helpers\Html;
use kartik\detail\DetailView;

?>

<?= DetailView::widget([
    'model' => $model,
    'hover' => false,
    'mode' => DetailView::MODE_VIEW,
    'attributes' => [
        [
            'attribute' => 'documentID',
            'format' => 'raw',
            'value' => $model->doc ? Html::a('Télécharger', $model->doc->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0]) : null,
        ],
        'tangibleAmount',
        'intangibleAmount',
        'stock',
        'workInProgress',
        'loansRecovery',
        'paidLeave:boolean',
        'other:ntext',
        'employersNumber',
    ],
]) ?>



