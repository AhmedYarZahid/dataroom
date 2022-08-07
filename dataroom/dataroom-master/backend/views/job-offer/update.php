<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\JobOffer */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-slideshare"></i>' . Yii::t('admin', 'Job Offers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Update');
?>
<div class="job-offer-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
