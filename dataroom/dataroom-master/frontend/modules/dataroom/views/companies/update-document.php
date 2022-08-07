<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\document\models\Document */


$this->title = Yii::t('admin', 'Document:') . ' ' . $model->title;
?>

<div class="document-update container">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_document-form', ['model' => $model]) ?>

</div>