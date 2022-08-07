<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\modules\dataroom\models\Room;

$this->title = 'AJAreclassement';
$this->params['breadcrumbs'][] = '<i class="fa fa-table"></i>&nbsp;&nbsp;' . $this->title;
?>

<div class="room-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Create {modelClass}', [
            'modelClass' => Yii::t('admin', 'Room'),
        ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'roomId',
                'label' => Yii::t('admin', 'Room ID'),
                'value' => function($model) {
                    return $model->room->id;
                }
            ],
            [
                'attribute' => 'roomTitle',
                'label' => Yii::t('admin', 'Room title'),
                'value' => function($model) {
                    return $model->room->title;
                }
            ],
            [
                'attribute' => 'managerEmail',
                'label' => Yii::t('admin', 'Manager email'),
                'value' => function($model) {
                    return $model->room->user->email;
                }
            ],
            [
                'attribute' => 'roomStatus',
                'label' => Yii::t('admin', 'Status'),
                'filter' => Room::statusList(),
                'value' => function($model) {
                    return $model->room->statusLabel();
                }
            ],
            [
                'class' => '\kartik\grid\ActionColumn',
                'template' => '{update} {stats} {send-email-to-users} {deactivate}',
                'buttons' => [
                    'stats' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-stats"></span>', $url, ['title' => Yii::t('admin', 'Stats'), 'data-pjax' => 0]);
                    },
                    'send-email-to-users' => function ($url, $model, $key) {
                        $url = ['/mailing/campaign/send-email-to-room-users', 'roomID' => $model->roomID];

                        return Html::a('<span class="fa fa-send-o"></span>', $url, ['title' => Yii::t('admin', 'Send email to users of room'), 'data-pjax' => 0]);
                    },
                    'deactivate' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-remove-circle"></span>', $url, [
                            'title' => Yii::t('admin', 'Deactivate'),
                            'data-pjax' => 0,
                            'data-confirm' => Yii::t('admin', 'Are you sure you want to deactivate this room?')]);
                    },
                ],
                'width' => '155px',
                'contentOptions' => ['class' => 'actions'],

                'visibleButtons' => [
                    'deactivate' => function ($model) {
                        return $model->state == \backend\modules\dataroom\models\RoomCV::STATE_READY;
                    },
                ]
            ],
        ],
    ]); ?>
</div>