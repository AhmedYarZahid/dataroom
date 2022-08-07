<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\modules\document\models\Document;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\document\models\DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model backend\modules\document\models\Document */

$this->title = Yii::t('notify', 'Documents');
$this->params['breadcrumbs'][] = '<i class="fa fa-file-pdf-o"></i> ' . $this->title;
?>
    <div class="document-index">

        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a(Yii::t('admin', 'Add {modelClass}', [
                'modelClass' => Yii::t('admin', 'Documents'),
            ]), ['create'], ['class' => 'btn btn-success']) ?>
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
                        return $model->getDocumentUrl(true) ? Html::a(Yii::t('admin', 'Download'), $model->getDocumentUrl(true), ['data-pjax' => 0]) : '';
                    },
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'options' => ['style' => 'width:10%;'],
                ],
                [
                    'header' => Yii::t('document', 'Link'),
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        return Html::button(Yii::t('admin', 'Copy'), ["class" => "btn btn-primary btn-xs copyToClipboard", "data-file-link" => $model->getDocumentUrlFrontend()]);
                    },
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                    'options' => ['style' => 'width:50px;']
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
                        'delete' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => Yii::t('admin', 'Delete'), 'data' => [
                                'confirm' => Yii::t('admin', 'Are you sure you want to delete this document?'),
                                'method' => 'post',
                            ]]);
                        },
                    ],
                    'options' => ['style' => 'width: 65px;'],

                    'visibleButtons' => [
                        'delete' => function ($model) {
                            return $model->type == Document::TYPE_REGULAR;
                        }
                    ]
                ],
            ],
        ]); ?>
    </div>

<?php $this->registerJs("
    $('body').on('click', '.copyToClipboard', function() {
        window.prompt('Appuyez sur \"Ctrl + C\" et puis cliquez sur \"Ok\" ou appuyez \"Entrée\".', $(this).data('fileLink'));
    });
"); ?>