<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<?php $this->beginContent('@app/views/layouts/main.php'); ?>

<div>
    <div class="tabs-wrapper" id="mailing-menu-wrapper">
        <?php echo \yii\bootstrap\Nav::widget([
            'encodeLabels' => false,
            'options' => ['class' => 'nav nav-tabs'],
            'items' => [
                [
                    'label' => Yii::t('admin', 'Mailing lists'),
                    'url' => \yii\helpers\Url::to(['list/index']),
                    'active' => ($this->context->id == 'list'),
                ],
                [
                    'label' => Yii::t('admin', 'Campaigns'),
                    'url' => \yii\helpers\Url::to(['campaign/index']),
                    'active' => ($this->context->id == 'campaign'),
                ],
            ],
        ]); ?>
    </div>

    <div>
        <?= $content; ?>
    </div>
</div>
<?php $this->endContent(); ?>
