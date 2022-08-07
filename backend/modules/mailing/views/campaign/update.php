<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\mailing\models\MailingCampaign */

$this->title = Yii::t('admin', 'Update {modelClass}', [
    'modelClass' => Yii::t('admin', 'Campaign'),
]);
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-send-o"></i> ' . Yii::t('admin', 'Campaigns'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="email-form">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>