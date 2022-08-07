<?php
/* @var $model \backend\modules\dataroom\models\RoomAccessRequestCoownership  */
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
            'attribute' => 'personType',
            'value' => $model->getPersonTypeCaption($model->personType)
        ],
        [
            'attribute' => 'identityCardID',
            'format' => 'raw',
            'value' => $model->identityCard
                ? \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->identityCard->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
                : null,
            'visible' => $model->personType == \backend\modules\dataroom\models\RoomAccessRequestCoownership::PERSON_TYPE_PHYSICAL
        ],
        [
            'attribute' => 'cvID',
            'format' => 'raw',
            'value' => $model->cv
                ? \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->cv->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
                : null,
            'visible' => $model->personType == \backend\modules\dataroom\models\RoomAccessRequestCoownership::PERSON_TYPE_PHYSICAL
        ],
        [
            'attribute' => 'lastTaxDeclarationID',
            'format' => 'raw',
            'value' => $model->lastTaxDeclaration
                ? \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->lastTaxDeclaration->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
                : null,
            'visible' => $model->personType == \backend\modules\dataroom\models\RoomAccessRequestCoownership::PERSON_TYPE_PHYSICAL
        ],
        [
            'attribute' => 'coownershipManagementReferenceID',
            'format' => 'raw',
            'value' => $model->coownershipManagementReference
                ? \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->coownershipManagementReference->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
                : null,
            'visible' => $model->personType == \backend\modules\dataroom\models\RoomAccessRequestCoownership::PERSON_TYPE_PHYSICAL
        ],
        [
            'attribute' => 'candidatePresentation',
            'format' => 'ntext',
            'visible' => $model->personType == \backend\modules\dataroom\models\RoomAccessRequestCoownership::PERSON_TYPE_LEGAL
        ],
        [
            'attribute' => 'groupPresentation',
            'format' => 'ntext',
            'visible' => $model->personType == \backend\modules\dataroom\models\RoomAccessRequestCoownership::PERSON_TYPE_LEGAL
        ],
        [
            'attribute' => 'kbisID',
            'format' => 'raw',
            'value' => $model->kbis
                ? \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->kbis->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
                : null,
            'visible' => $model->personType == \backend\modules\dataroom\models\RoomAccessRequestCoownership::PERSON_TYPE_LEGAL
        ],
        [
            'attribute' => 'latestCertifiedAccountsID',
            'format' => 'raw',
            'value' => $model->latestCertifiedAccounts
                ? \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->latestCertifiedAccounts->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
                : null,
            'visible' => $model->personType == \backend\modules\dataroom\models\RoomAccessRequestCoownership::PERSON_TYPE_LEGAL
        ],
        [
            'attribute' => 'capitalAllocationID',
            'format' => 'raw',
            'value' => $model->capitalAllocation
                ? \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->capitalAllocation->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
                : null,
            'visible' => $model->personType == \backend\modules\dataroom\models\RoomAccessRequestCoownership::PERSON_TYPE_LEGAL
        ],
        [
            'attribute' => 'agreementID',
            'format' => 'raw',
            'value' => $model->agreement
                ? \kartik\helpers\Html::a(Yii::t('admin', 'Download'), $model->agreement->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0])
                : null
        ],
    ],
]); ?>



