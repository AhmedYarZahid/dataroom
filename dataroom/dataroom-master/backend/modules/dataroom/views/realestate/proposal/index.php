<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\datecontrol\DateControl;
use common\helpers\DateHelper;

$this->title = Yii::t('admin', 'Proposals');
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-table"></i>AJAimmo', 'url' => ['realestate/room/index']];
$this->params['breadcrumbs'][] = '<i class="fa fa-table"></i> ' . $this->title;
?>

<div class="room-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'proposalID',
            [
                'attribute' => 'roomTitle',
                'label' => Yii::t('admin', 'Room'),
                'value' => function($model) {
                    return $model->proposal->room->title;
                }
            ],
            [
                'attribute' => 'userEmail',
                'label' => Yii::t('admin', 'Buyer email'),
                'value' => function($model) {
                    return $model->proposal->user->email;
                }
            ],
            [
                'attribute' => 'creatorEmail',
                'label' => Yii::t('admin', 'Created by'),
                'value' => function($model) {
                    return $model->proposal->creator->email;
                }
            ],
            [
                'attribute' => 'createdDate',
                'label' => Yii::t('admin', 'Date'),
                'filterType' => DateControl::class,
                'value' => function($model) {
                    return DateHelper::getFrenchFormatDbDate($model->proposal->createdDate, true);
                }
            ],
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function ($model, $key, $index, $column) {
                    return $this->render('_expanded', ['model' => $model]);
                },
                'expandOneOnly' => true,
                'detailAnimationDuration' => 0,
            ],
        ],
    ]); ?>
</div>