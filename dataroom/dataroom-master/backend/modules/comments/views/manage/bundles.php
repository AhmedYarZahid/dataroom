<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use backend\modules\comments\CommentAdminAsset;
use backend\modules\comments\models\CommentBundle;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\comments\models\CommentBundleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model backend\modules\comments\models\CommentBundle */

CommentAdminAsset::register($this);

$this->title = Yii::t('notify', 'Comments');
$this->params['breadcrumbs'][] = '<i class="fa fa-commentspaper-o"></i> ' . $this->title;
?>
<div class="comments-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => false,
        'columns' => [
            'nodeID',
            [
                'attribute' => 'nodeType',
                'filter' => CommentBundle::getTypes(),
                'value' => function($model) {
                    return CommentBundle::translateType($model->nodeType);
                },
            ],
            'nodeTitle',
            [
                'class' => '\kartik\grid\BooleanColumn',
                'attribute' => 'isActive',
                'contentOptions' => function($model) {
                    return [
                        'class' => 'boolean-toggler',
                        'data-url' => Url::to(['/comments/manage/toggle-bundle-activity', 'id' => $model->id]),
                    ];
                },
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'class' => '\kartik\grid\BooleanColumn',
                'attribute' => 'isNewCommentsAllowed',
                'contentOptions' => function($model) {
                    return [
                        'class' => 'boolean-toggler',
                        'data-url' => Url::to(['/comments/manage/toggle-bundle-is-new-comments-allowed', 'id' => $model->id]),
                    ];
                },
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
                'template' => '{viewComments}{updateBundle}',
                'buttons' => [
                    'viewComments' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => Yii::t('app', 'View Comments'),
                        ]);
                    },
                    'updateBundle' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('app', 'Update Comments Settings'),
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'viewComments') {
                        return Url::toRoute(['/comments/manage/comments', 'id' => $model->id]);
                    } elseif ($action === 'updateBundle') {
                        return Url::toRoute(['/comments/manage/update-bundle', 'id' => $model->id]);
                    }

                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'options' => ['style' => 'width: 65px;'],
            ],
        ],
    ]); ?>
</div>