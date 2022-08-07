<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\mailing\models\MailingCampaign */
/* @var $roomModel \backend\modules\dataroom\models\Room */

$this->title = Yii::t('admin', 'Send email to users of room «{roomName}»', ['roomName' => $roomModel->title]);
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-table"></i> ' . Yii::t('admin', 'Rooms'), 'url' => ['/dataroom']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="email-form">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'roomModel' => $roomModel,
        'recipientIDs' => $recipientIDs
    ]) ?>

</div>