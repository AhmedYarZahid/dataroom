<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\document\models\Document */

$this->title = Yii::t('admin', 'Add Document');
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-table"></i> AJArepreneurs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-file-pdf-o"></i> ' . Yii::t('admin', 'Room Documents'), 'url' => ['documents', 'id' => $this->context->detailedRoomModel->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="document-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_document-form', ['model' => $model]) ?>

</div>
