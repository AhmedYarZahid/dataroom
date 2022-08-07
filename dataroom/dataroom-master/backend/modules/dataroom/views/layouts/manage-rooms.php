<?php

use yii\helpers\Html;
use backend\modules\dataroom\Module as DataroomModule;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<?php $this->beginContent('@app/views/layouts/main.php'); ?>

<div>
    <div class="tabs-wrapper" id="rooms-menu-wrapper">
        <?php echo \yii\bootstrap\Nav::widget([
            'encodeLabels' => false,
            'options' => ['class' => 'nav nav-tabs room-space'],
            'items' => [
                [
                    'label' => Yii::t('admin', 'Rooms'),
                    'url' => \yii\helpers\Url::to([$this->context->roomType . '/room/index']),
                    'active' => ($this->context->controllerID == 'room'),
                ],
                [
                    'label' => Yii::t('admin', 'Access requests'),
                    'url' => \yii\helpers\Url::to([$this->context->roomType . '/access-request/index']),
                    'active' => ($this->context->controllerID == 'access-request'),
                ],
                [
                    'label' => Yii::t('admin', 'Proposals'),
                    'url' => \yii\helpers\Url::to([$this->context->roomType . '/proposal/index']),
                    'active' => ($this->context->controllerID == 'proposal'),
                    'visible' => $this->context->roomType != DataroomModule::SECTION_CV
                ],
            ],
        ]); ?>
    </div>

    <div>
        <?= $content; ?>
    </div>
</div>
<?php $this->endContent(); ?>
