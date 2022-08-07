<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\notify\models\Notify */

$this->title = Yii::t('admin', 'Notification:') . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-envelope-o"></i> ' . Yii::t('admin', 'Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title];
?>
<div class="notify-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

