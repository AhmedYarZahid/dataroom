<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\datecontrol\DateControl;
use common\helpers\DateHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\mailing\models\MailingListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Mailing lists');
$this->params['breadcrumbs'][] = '<i class="fa fa-send-o"></i> ' . $this->title;

?>

<div class="mailing-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Create {modelClass}', [
            'modelClass' => Yii::t('admin', 'Mailing list'),
        ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            [
                'attribute' => 'createdByUserID',
                'value' => function ($model) {
                    return $model->createdByUser->email;
                },
            ],
            [
                'attribute' => 'updatedDate',
                'filterType' => DateControl::class,
                'value' => function($model) {
                    return DateHelper::getFrenchFormatDbDate($model->updatedDate, true);
                }
            ],

            [
                'class' => '\kartik\grid\ActionColumn',
                'template' => '{view} {update} {delete} {send}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => Yii::t('admin', 'Update'), 'data-pjax' => 0]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => Yii::t('admin', 'Delete'), 'data' => [
                            'confirm' => Yii::t('admin', 'Are you sure you want to delete this user?'),
                            'method' => 'post',
                        ]]);
                    },
                    'send' => function($url, $model, $key) {
                        $url = ['campaign/create', 'listID' => $model->id];
                        
                        return Html::a('<span class="glyphicon glyphicon-send"></span>', $url, ['title' => Yii::t('admin', 'Send a new email'), 'data-pjax' => 0]);
                    }
                ],
                //'deleteOptions' => ['message' => 'Custom message'],
                'options' => ['style' => 'width: 100px;']
            ],
        ],
    ]); ?>

</div>