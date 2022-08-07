<?php

use backend\modules\dataroom\Module as DataroomModule;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<?php $this->beginContent('@app/views/layouts/main.php'); ?>

<div>
    <div class="tabs-wrapper" id="room-menu-wrapper">
        <?php echo \yii\bootstrap\Nav::widget([
            'encodeLabels' => false,
            'options' => ['class' => 'nav nav-tabs room-space'],
            'items' => [
                [
                    'label' => Yii::t('admin', 'General'),
                    'url' => \yii\helpers\Url::to(['update', 'id' => $this->context->detailedRoomModel->id]),
                    'active' => ($this->context->action->id == 'update'),
                ],
                [
                    'label' => Yii::t('admin', 'Documents'),
                    'url' => \yii\helpers\Url::to([$this->context->detailedRoomModel->room->section == DataroomModule::SECTION_COMPANIES
                        ? 'manage-documents-tree'
                        : 'documents',
                        'id' => $this->context->detailedRoomModel->id
                    ]),
                    'active' => in_array($this->context->action->id, ['documents', 'create-document', 'update-document', 'create-multiple-documents', 'manage-documents-tree']),
                    'visible' => $this->context->detailedRoomModel->room->section != DataroomModule::SECTION_CV
                ],
                [
                    'label' => Yii::t('admin', 'Proposals'),
                    'url' => \yii\helpers\Url::to(['proposals', 'id' => $this->context->detailedRoomModel->id]),
                    'active' => in_array($this->context->action->id, ['proposals', 'create-proposal']),
                    'visible' => $this->context->detailedRoomModel->room->section != DataroomModule::SECTION_CV
                ],
                [
                    'label' => Yii::t('admin', 'Stats'),
                    'url' => \yii\helpers\Url::to(['stats', 'id' => $this->context->detailedRoomModel->id]),
                    'active' => in_array($this->context->action->id, ['stats']),
                ],
            ],
        ]); ?>
    </div>

    <div>
        <?= $content; ?>
    </div>
</div>
<?php $this->endContent(); ?>
