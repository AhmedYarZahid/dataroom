<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\mailing\models\MailingList */

$this->title = Yii::t('admin', 'Update {modelClass}', [
    'modelClass' => Yii::t('admin', 'Mailing list'),
]);
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-send-o"></i> ' . Yii::t('admin', 'Mailing lists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="mailing-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'contactForm' => $contactForm,
    ]) ?>

</div>