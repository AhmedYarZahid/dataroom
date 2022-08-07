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
        [
            'attribute' => 'agreementID',
            'format' => 'raw',
            'value' => \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->agreement->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
        ],
    ],
]); ?>



