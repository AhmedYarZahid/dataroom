<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $model \backend\modules\dataroom\models\ProposalCoownership  */

?>

<?= DetailView::widget([
    'model' => $model,
    'hover' => false,
    'mode' => DetailView::MODE_VIEW,
    'attributes' => [
        [
            'attribute' => 'documentID',
            'format' => 'raw',
            'value' => $model->document ? Html::a(Yii::t('admin', 'Download'), $model->document->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0]) : null,
        ],
        'companyName',
        'fullName',
        'address',
        'phone',
        [
            'attribute' => 'kbisID',
            'format' => 'raw',
            'value' => $model->kbis
                ? \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->kbis->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
                : null,
        ],
        [
            'attribute' => 'cniID',
            'format' => 'raw',
            'value' => $model->cni
                ? \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->cni->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
                : null,
        ],
        [
            'attribute' => 'businessCardID',
            'format' => 'raw',
            'value' => $model->businessCard
                ? \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->businessCard->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
                : null,
        ],
    ],
]) ?>



