<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Newsletter */

$this->title = Yii::t('admin', 'Add Subscription');
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-newspaper-o"></i>' . Yii::t('admin', 'Newsletter Subscribers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="newsletter-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
