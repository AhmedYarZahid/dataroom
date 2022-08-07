<?php
/* @var $model \backend\modules\dataroom\models\RoomAccessRequestCV  */
?>

<?= \kartik\detail\DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label' => $model->accessRequest->user->getAttributeLabel('firstName'),
            'value' => $model->accessRequest->user->firstName
        ],
        [
            'label' => $model->accessRequest->user->getAttributeLabel('lastName'),
            'value' => $model->accessRequest->user->lastName
        ],
        [
            'label' => $model->accessRequest->user->getAttributeLabel('phone'),
            'value' => $model->accessRequest->user->phone
        ],
        [
            'label' => $model->accessRequest->user->getAttributeLabel('companyName'),
            'value' => $model->accessRequest->user->companyName
        ],
        'presentation:ntext',
        [
            'attribute' => 'kbis',
            'format' => 'raw',
            'value' => \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->kbisDoc->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
        ],
        [
            'attribute' => 'balanceSheet',
            'format' => 'raw',
            'value' => \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->balanceSheetDoc->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
        ],
        [
            'attribute' => 'cni',
            'format' => 'raw',
            'value' => \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->cniDoc->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
        ],
        [
            'attribute' => 'commitment',
            'format' => 'raw',
            'value' => \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->commitmentDoc->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
        ],
    ],
]); ?>
