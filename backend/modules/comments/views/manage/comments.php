<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use backend\modules\comments\CommentAdminAsset;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\comments\models\CommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model backend\modules\comments\models\Comment */

CommentAdminAsset::register($this);

$this->title = Yii::t('notify', 'Comments');
$this->params['breadcrumbs'][] = Html::a('<i class="fa fa-commentspaper-o"></i> ' . $this->title, '/comments/manage');

?>
<div class="comments-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => false,
        'columns' => [
            'id',
            'authorName',
            'authorEmail',
            'text',
            [
                'class' => '\kartik\grid\BooleanColumn',
                'attribute' => 'isApproved',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'contentOptions' => function($model) {
                    return [
                        'class' => 'boolean-toggler',
                        'data-url' => Url::to(['/comments/manage/toggle-comment-approval', 'id' => $model->id]),
                    ];
                },
            ],
            [
                'attribute' => 'createdDate',
                'format' => ['date', 'php:d/m/Y'],
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{updateComment} {deleteComment}',
                'buttons' => [
                    'updateComment' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('app', 'Update Comment'),
                        ]);
                    },
                    'deleteComment' => function($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'title' => Yii::t('app', 'Delete Comment'),
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'updateComment') {
                        return Url::toRoute(['/comments/manage/update-comment', 'id' => $model->id]);
                    } elseif ($action === 'deleteComment') {
                        return Url::toRoute(['/comments/manage/delete-comment', 'id' => $model->id]);
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