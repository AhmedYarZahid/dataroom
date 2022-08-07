<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\JobOffer */

$this->title = Yii::t('admin', 'Add Job Offer');
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-slideshare"></i>' . Yii::t('admin', 'Job Offers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="job-offer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
