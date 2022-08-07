<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\modules\document\models\Document;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\document\models\DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model backend\modules\document\models\Document */

$this->title = Yii::t('admin', 'Room Documents Tree');
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-table"></i> AJArepreneurs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-file-pdf-o"></i> ' . Yii::t('admin', 'Room Documents'), 'url' => ['documents', 'id' => $this->context->detailedRoomModel->id]];
$this->params['breadcrumbs'][] = '<i class="fa fa-file-pdf-o"></i> ' . $this->title;
?>
<div class="manage-document-folders">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php  // Html::a(Yii::t('admin', 'Add Document'), ['create-document', 'id' => $this->context->detailedRoomModel->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('admin', 'Add Multiple Documents'), ['create-multiple-documents', 'id' => $this->context->detailedRoomModel->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('admin', 'Documents List'), ['documents', 'id' => $this->context->detailedRoomModel->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <div class="hint-block"><?= Yii::t('admin', 'Edit titles with `double click`, `Shift + click` [F2], or [Enter] (on Mac only). Also a `slow click` (click again into already active node).') ?></div>

    <?= $this->render('_documents-tree', [
        'detailedRoomModel' => $this->context->detailedRoomModel,
        'allowManage' => true
    ]) ?>

    <br>

    <p>
        <?= Html::a(Yii::t('admin', 'Add child folder'), 'javascript:void(0);', ['class' => 'btn btn-xs btn-info', 'id' => 'add-child-folder']) ?>&nbsp;
        <?= Html::a(Yii::t('admin', 'Add sibling folder'), 'javascript:void(0);', ['class' => 'btn btn-xs btn-info', 'id' => 'add-sibling-folder']) ?>
        &nbsp;&nbsp;
        <!-- <?= Html::a(Yii::t('admin', 'Update document'), 'javascript:void(0);', ['class' => 'btn btn-xs btn-warning', 'id' => 'update-document']) ?> -->
        <?= Html::a(Yii::t('admin', 'Remove folder/document'), 'javascript:void(0);', ['class' => 'btn btn-xs btn-danger', 'id' => 'remove-node']) ?>
    </p>
    <p>
        <!--<?/*= Html::a(Yii::t('admin', 'Download document'), 'javascript:void(0);', ['class' => 'btn btn-xs btn-velvet', 'id' => 'download-document']) */?>-->
        <?= Html::a(Yii::t('admin', 'Download selected documents'), 'javascript:void(0);', ['class' => 'btn btn-xs btn-velvet', 'id' => 'download-selected-documents']) ?>
        <?= Html::a(Yii::t('admin', 'Select all documents'), '#', ['id' => 'btnSelectAll','class' => 'btn btn-xs btn-velvet']) ?>
    </p>

</div>