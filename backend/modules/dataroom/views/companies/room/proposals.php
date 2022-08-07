<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\datecontrol\DateControl;
use common\helpers\DateHelper;

$this->title = Yii::t('admin', 'Proposals');
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-table"></i> AJArepreneurs', 'url' => ['index']];
$this->params['breadcrumbs'][] = '<i class="fa fa-book"></i> ' . $this->title;
?>

<div class="room-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Add Proposal'), ['companies/proposal/create', 'roomId' => $this->context->detailedRoomModel->roomID], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'showPageSummary' => true,
        'columns' => [
            [
                'attribute' => 'createdDate',
                'label' => Yii::t('admin', 'Date'),
                'filterType' => DateControl::class,
                'value' => function($model) {
                    return DateHelper::getFrenchFormatDbDate($model->proposal->createdDate, true);
                },
                'hAlign' => 'center',
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'documentID',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->doc ? Html::a('Télécharger', $model->doc->getDocumentUrl(), ['target' => '_blank', 'data-pjax' => 0]) : null;
                },
                'hAlign' => 'center',
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'tangibleAmount',
                'pageSummary' => true,
                'hAlign' => 'center',
                'vAlign' => 'middle'
            ],

            [
                'attribute' => 'intangibleAmount',
                'pageSummary' => true,
                'hAlign' => 'center',
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'stock',
                'pageSummary' => true,
                'hAlign' => 'center',
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'workInProgress',
                'pageSummary' => true,
                'hAlign' => 'center',
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'loansRecovery',
                'pageSummary' => true,
                'hAlign' => 'center',
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'employersNumber',
                'pageSummary' => true,
                'hAlign' => 'center',
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'paidLeave',
                'format' => 'boolean',
                'hAlign' => 'center',
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'other',
                'format' => 'ntext',
                'hAlign' => 'left',
                'vAlign' => 'top'
            ],
            [
                'attribute' => 'userEmail',
                'label' => Yii::t('admin', 'Buyer email'),
                'value' => function($model) {
                    return $model->proposal->user->email;
                },
                'hAlign' => 'center',
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'creatorEmail',
                'label' => Yii::t('admin', 'Created by'),
                'value' => function($model) {
                    return $model->proposal->creator->email;
                },
                'hAlign' => 'center',
                'vAlign' => 'middle'
            ],
            /*[
                'class' => 'kartik\grid\ExpandRowColumn',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function ($model, $key, $index, $column) {
                    return $this->render('/companies/proposal/_expanded', ['model' => $model]);
                },
                'expandOneOnly' => true,
                'detailAnimationDuration' => 0,
            ],*/
        ],
    ]); ?>
</div>