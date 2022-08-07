<?php

use backend\modules\dataroom\Module as DataroomModule;

/* @var $this \yii\web\View */
/* @var $content string */

$this->title = Yii::t('app', 'My rooms');
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
                    'url' => \yii\helpers\Url::to(['my-rooms', 'section' => DataroomModule::SECTION_COMPANIES]),
                    'active' => (!Yii::$app->request->get('section') || Yii::$app->request->get('section') == DataroomModule::SECTION_COMPANIES),
                ],
                [
                    'label' => Yii::t('app', 'Real Estate Rooms'),
                    'url' => \yii\helpers\Url::to(['my-rooms', 'section' => DataroomModule::SECTION_REAL_ESTATE]),
                    'active' => (Yii::$app->request->get('section') == DataroomModule::SECTION_REAL_ESTATE),
                ],
                [
                    'label' => Yii::t('app', 'Co-ownership Rooms'),
                    'url' => \yii\helpers\Url::to(['my-rooms', 'section' => DataroomModule::SECTION_COOWNERSHIP]),
                    'active' => (Yii::$app->request->get('section') == DataroomModule::SECTION_COOWNERSHIP),
                ],
                [
                    'label' => Yii::t('app', 'CV Rooms'),
                    'url' => \yii\helpers\Url::to(['my-rooms', 'section' => DataroomModule::SECTION_CV]),
                    'active' => (Yii::$app->request->get('section') == DataroomModule::SECTION_CV),
                ],
            ],
        ]); ?>
        <br>
    </div>

    <div>
        <?= $content; ?>
    </div>
</div>
<?php $this->endContent(); ?>
