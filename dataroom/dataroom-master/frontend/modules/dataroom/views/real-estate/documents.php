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
            <?php if ($dataProvider->count): ?>
                <?= Html::a(Yii::t('admin', 'Download all docs'), ['download-all-documents', 'roomID' => $this->context->detailedRoomModel->id], ['class' => 'btn add-document-btn', 'target' => '_blank']) ?>
                &nbsp;&nbsp;
            <?php endif ?>

            <?php if (Yii::$app->user->can('manager')): ?>
                <?= Html::a(Yii::t('admin', 'Add Document'), ['create-document', 'id' => $this->context->detailedRoomModel->id], ['class' => 'btn btn-success']) ?>
                <?= Html::a(Yii::t('admin', 'Add Multiple Documents'), ['create-multiple-documents', 'id' => $this->context->detailedRoomModel->id], ['class' => 'btn btn-success']) ?>
            <?php endif ?>
        </p>

        <?= GridView::widget([
            'id' => 'documents-list-grid',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'class' => '\kartik\grid\CheckboxColumn'
                ],
                'title',
                [
                    'header' => Yii::t('document', 'Download'),
                    'format' => 'raw',
                    'enableSorting' => false,
                    'value' => function ($model) {
                        return $model->getDocumentUrl() ? Html::a(Yii::t('admin', 'Download'), $model->getDocumentUrl(true), ['data-pjax' => 0]) : '';
                    },
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'options' => ['style' => 'width:10%;'],
                ],
                /*[
                    'header' => Yii::t('document', 'Link'),
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        return Html::button(Yii::t('admin', 'Copy'), ["class" => "btn btn-primary btn-xs copyToClipboard", "data-file-link" => $model->getDocumentUrl()]);
                    },
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'options' => ['style' => 'width:50px;']
                ],*/
                [
                    'header' => Yii::t('document', 'Size'),
                    'attribute' => 'size',
                    'format' => 'shortSize',
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'options' => ['style' => 'width:150px;'],
                ],
                [
                    'attribute' => 'publishDate',
                    'format' => ['date', 'php:d/m/Y'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                ],
                [
                    'class' => '\kartik\grid\BooleanColumn',
                    'header' => Yii::t('document', 'Published'),
                    'attribute' => 'fIsPublished',
                    'filter' => \common\helpers\ArrayHelper::getYesNoList(),
                    'value' => function ($model, $key, $index, $column) {
                        return $model->getIsPublished();
                    },
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'trueLabel' => Yii::t('document', 'Published'),
                    'falseLabel' => Yii::t('document', 'Not Published'),
                    'visible' => Yii::$app->user->can('manager')
                ],
                [
                    'class' => '\kartik\grid\BooleanColumn',
                    'attribute' => 'isActive',
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'visible' => Yii::$app->user->can('manager')
                ],
                [
                    'attribute' => 'rank',
                    'options' => ['style' => 'width:70px;'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'visible' => Yii::$app->user->can('manager')
                ],
                [
                    'attribute' => 'updatedDate',
                    'format' => ['date', 'php:d/m/Y'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'visible' => Yii::$app->user->can('manager')
                ],
                /*[
                    'class' => '\kartik\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url, $model, $key) {
                            return  Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update-document', 'documentID' => $model->id], ['title' => Yii::t('admin', 'Update'), 'data' => ['pjax' => 0]]);
                        },
                        'delete' => function ($url, $model, $key) {
                            return  Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete-document', 'documentID' => $model->id], ['title' => Yii::t('admin', 'Delete'), 'data' => [
                                'confirm' => Yii::t('admin', 'Are you sure you want to delete this document?'),
                                'method' => 'post',
                            ]]);
                        }
                    ],
                    'options' => ['style' => 'width: 65px;']
                ],*/
            ],
        ]); ?>

        <?= Html::a(Yii::t('admin', 'Download selected documents'), 'javascript:void(0);', ['class' => 'btn add-document-btn', 'id' => 'download-selected-documents']) ?>
        <br><br>
    </div>

<?php $this->registerJs("
    $('body').on('click', '.copyToClipboard', function() {
        window.prompt('Appuyez sur \"Ctrl + C\" et puis cliquez sur \"Ok\" ou appuyez \"Entrée\".', $(this).data('fileLink'));
    });
"); ?>

<?php $this->registerJs('
$("body").on("click", "#download-selected-documents", function(event) {
    documentIDs = $("#documents-list-grid").yiiGridView("getSelectedRows");

    if (!documentIDs.length) {
        alert("' . Yii::t('admin', 'Please choose at least one document.') . '");
        return false;
    }

    var downloadUrl = "' . (Yii::$app->id == 'app-frontend' ? 'dataroom/real-estate/download-all-documents' : 'dataroom/real-estate/room/download-all-documents'). '";
    window.open(UrlManager.createUrl(downloadUrl, {roomID: ' . $this->context->detailedRoomModel->id . ', idList: documentIDs}));
});
') ?>