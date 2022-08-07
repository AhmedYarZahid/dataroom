<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel \backend\modules\document\models\DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Downloads');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-index container">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'filterModel' => null,
        'columns' => [
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->title, ['download', 'id' => $model->id], ['data-pjax' => 0]);
                },
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'attribute' => 'publishDate',
                'format' => ['date', 'php:d/m/Y'],
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'attribute' => 'comment',
                'format' => 'ntext',
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
        ],
    ]); ?>

</div>
