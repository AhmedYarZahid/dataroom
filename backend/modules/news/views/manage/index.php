<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\modules\news\models\News;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\news\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model backend\modules\news\models\News */

$this->title = Yii::t('notify', 'News');
$this->params['breadcrumbs'][] = '<i class="fa fa-newspaper-o"></i> ' . $this->title;
?>
<div class="news-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Add {modelClass}', [
            'modelClass' => Yii::t('admin', 'News'),
        ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'title',
            [
                'attribute' => 'category',
                'filter' => $searchModel->categoryList(),
                'value' => function($model) {
                    return $model->categoryLabel;
                },
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
                'trueLabel' => Yii::t('document', 'Published'),
                'falseLabel' => Yii::t('document', 'Not Published'),
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'class' => '\kartik\grid\BooleanColumn',
                'attribute' => 'isActive',
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'attribute' => 'createdDate',
                'format' => ['date', 'php:d/m/Y'],
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{update} {delete}',
                /*'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return !$model->isDefault ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => Yii::t('notify', 'Delete'), 'data' => [
                            'confirm' => Yii::t('admin', 'Are you sure you want to delete this notification?'),
                            'method' => 'post',
                        ]]) : '';
                    }
                ],*/
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'options' => ['style' => 'width: 65px;'],
            ],
        ],
    ]); ?>
</div>