<?php

/* @var $this \yii\web\View */
/* @var $content string */

$this->title = Yii::t('app', 'Our offers');
?>

<?php $this->beginContent('@app/modules/dataroom/views/layouts/main.php'); ?>

<div class="rooms-list">

    <div class="top-header container">
        <h1 class="page-header"><?= $this->title ?></h1>
    </div>

    <div class="tabs-wrapper" id="room-menu-wrapper">
        <?php echo \yii\bootstrap\Nav::widget([
            'encodeLabels' => false,
            'options' => ['class' => 'nav nav-tabs room-space container'],
            'items' => [
                [
                    'label' => Yii::t('app', 'Company Rooms'),
                    'url' => \yii\helpers\Url::to(['/dataroom/companies']),
                    'active' => ($this->context->id == 'companies' && $this->context->action->id == 'index'),
                ],
                [
                    'label' => Yii::t('app', 'Real Estate Rooms'),
                    'url' => \yii\helpers\Url::to(['/dataroom/real-estate']),
                    'active' => ($this->context->id == 'real-estate' && $this->context->action->id == 'index'),
                ],
                [
                    'label' => Yii::t('app', 'Co-ownership Rooms'),
                    'url' => \yii\helpers\Url::to(['/dataroom/coownership']),
                    'active' => ($this->context->id == 'coownership' && $this->context->action->id == 'index'),
                ],
                [
                    'label' => Yii::t('app', 'CV Rooms'),
                    'url' => \yii\helpers\Url::to(['/dataroom/cv']),
                    'active' => ($this->context->id == 'cv' && $this->context->action->id == 'index'),
                ],
            ],
        ]); ?>
    </div>

    <div>
        <?= $content; ?>
    </div>
</div>
<?php $this->endContent(); ?>
