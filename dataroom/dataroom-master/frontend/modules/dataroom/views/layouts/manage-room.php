<?php

use backend\modules\dataroom\Module as DataroomModule;

/* @var $this \yii\web\View */
/* @var $content string */

$detailedRoomModel = $this->context->detailedRoomModel;

$tabItems = [
    'general' => [
        'label' => Yii::t('app', 'General'),
        'url' => \yii\helpers\Url::to(['view-room', 'id' => $detailedRoomModel->id]),
        'active' => (in_array($this->context->action->id, ['view-room', 'update-room'])),
    ],
];

if (Yii::$app->user->can('updateRoom', ['room' => $detailedRoomModel->room])) {
    /*$tabItems += [
        'update' => [
            'label' => Yii::t('app', 'Update room'),
            'url' => \yii\helpers\Url::to(['update-room', 'id' => $detailedRoomModel->id]),
            'active' => ($this->context->action->id == 'update-room'),
        ],
    ];*/
}

if (Yii::$app->user->can('seeRoomDetails', ['room' => $detailedRoomModel->room]) && $detailedRoomModel->room->section != DataroomModule::SECTION_CV) {
    $tabItems += [
        'documents' => [
            'label' => Yii::t('app', 'Documents'),
            'url' => \yii\helpers\Url::to([$detailedRoomModel->room->section == DataroomModule::SECTION_COMPANIES
                ? 'manage-documents-tree'
                : 'documents',
                'id' => $detailedRoomModel->id
            ]),
            'active' => in_array($this->context->action->id, ['manage-documents-tree', 'documents', 'create-document', 'create-multiple-documents', 'update-document']),
        ],
    ];
}

?>

<?php $this->beginContent('@app/modules/dataroom/views/layouts/main.php'); ?>

<div>
    <?php if (count($tabItems) > 1) : ?>
    <div class="tabs-wrapper" id="room-menu-wrapper">
        <?php echo \yii\bootstrap\Nav::widget([
            'encodeLabels' => false,
            'options' => ['class' => 'nav nav-tabs room-space container'],
            'items' => $tabItems,
        ]); ?>
    </div>
    <?php endif ?>

    <div>
        <?= $content; ?>
    </div>
</div>
<?php $this->endContent(); ?>
