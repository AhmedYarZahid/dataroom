<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\modules\notify\models\Notify;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\notify\models\NotifySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('notify', 'Notifications');
$this->params['breadcrumbs'][] = '<i class="fa fa-envelope-o"></i> ' . $this->title;
?>
<div class="notify-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Create a notification'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php //\yii\widgets\Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'eventID',
                'filter' => Notify::getEventFilter(),
                'value' => function ($model, $key, $index, $column) {
                    return Notify::$eventCaptions[$model->eventID]["caption"];
                },
            ],
            'title',
            [
                'class' => '\kartik\grid\BooleanColumn',
                'attribute' => 'isDefault',
                'trueLabel' => Yii::t('notify', 'Default'),
                'falseLabel' => Yii::t('notify', 'Not Default'),
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        return !$model->isDefault ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => Yii::t('notify', 'Delete'), 'data' => [
                            'confirm' => Yii::t('admin', 'Are you sure you want to delete this notification?'),
                            'method' => 'post',
                        ]]) : '';
                    }
                ],
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'options' => ['style' => 'width: 50px;']
            ],
        ],
    ]); ?>
    <?php //\yii\widgets\Pjax::end(); ?>
</div>