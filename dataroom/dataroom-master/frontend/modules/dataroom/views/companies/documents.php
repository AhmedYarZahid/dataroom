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
            <?= Html::a(Yii::t('admin', 'Download all docs'), ['create-document', 'id' => $this->context->detailedRoomModel->id], ['class' => 'btn add-document-btn']) ?>
        </p>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
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
                [
                    'header' => Yii::t('document', 'Link'),
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        return Html::button(Yii::t('admin', 'Copy'), ["class" => "btn btn-primary btn-xs copyToClipboard", "data-file-link" => $model->getDocumentUrl()]);
                    },
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'options' => ['style' => 'width:50px;']
                ],
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
                ],
                [
                    'class' => '\kartik\grid\BooleanColumn',
                    'attribute' => 'isActive',
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                ],
                [
                    'attribute' => 'rank',
                    'options' => ['style' => 'width:70px;'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                ],
                [
                    'attribute' => 'updatedDate',
                    'format' => ['date', 'php:d/m/Y'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                ],
                [
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
                ],
            ],
        ]); ?>
    </div>

<?php $this->registerJs("
    $('body').on('click', '.copyToClipboard', function() {
        window.prompt('Appuyez sur \"Ctrl + C\" et puis cliquez sur \"Ok\" ou appuyez \"Entrée\".', $(this).data('fileLink'));
    });
"); ?>