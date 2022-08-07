<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\modules\document\models\Document;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\document\models\DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model backend\modules\document\models\Document */

$this->title = Yii::t('admin', 'Room Documents');
?>
    <div class="document-index container">

        <h1 class="page-heading"><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a(Yii::t('admin', 'Download selected documents'), 'javascript:void(0);', ['class' => 'btn add-document-btn', 'id' => 'download-selected-documents']) ?>
            <?= Html::a(Yii::t('admin', 'Download all docs'), ['download-all-documents', 'roomID' => $this->context->detailedRoomModel->id], ['class' => 'btn add-document-btn', 'target' => '_blank']) ?>

            <?php if (Yii::$app->user->can('manager') || Yii::$app->user->can('admin')): ?>
                &nbsp;&nbsp;
                <?= Html::a(Yii::t('admin', 'Add Document'), ['create-document', 'id' => $this->context->detailedRoomModel->id], ['class' => 'btn btn-success']) ?>
                <?= Html::a(Yii::t('admin', 'Add Multiple Documents'), ['create-multiple-documents', 'id' => $this->context->detailedRoomModel->id], ['class' => 'btn btn-success']) ?>
            <?php endif ?>
        </p>

        <?= $this->render('@backend/modules/dataroom/views/companies/room/_documents-tree', [
            'detailedRoomModel' => $this->context->detailedRoomModel,
            'allowManage' => Yii::$app->user->can('admin'),
            'addDatesInfo' => true
        ]) ?>

        <p style="padding-top: 10px;">
            <?php if (Yii::$app->user->can('admin')): ?>
                <?= Html::a(Yii::t('admin', 'Add child folder'), 'javascript:void(0);', ['class' => 'btn btn-info', 'id' => 'add-child-folder']) ?>&nbsp;
                <?= Html::a(Yii::t('admin', 'Add sibling folder'), 'javascript:void(0);', ['class' => 'btn btn-info', 'id' => 'add-sibling-folder']) ?>
                &nbsp;&nbsp;
                <?= Html::a(Yii::t('admin', 'Update document'), 'javascript:void(0);', ['class' => 'btn btn-warning', 'id' => 'update-document']) ?>
                <?= Html::a(Yii::t('admin', 'Remove folder/document'), 'javascript:void(0);', ['class' => 'btn btn-danger', 'id' => 'remove-node']) ?>
            <?php endif ?>
        </p>

    </div>