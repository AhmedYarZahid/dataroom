<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Newsletter */

$this->title = $model->email;
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-newspaper-o"></i>' . Yii::t('admin', 'Newsletter Subscribers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->email, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Update');
?>
<div class="newsletter-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
