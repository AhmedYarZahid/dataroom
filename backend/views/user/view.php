<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\grid\GridView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->getFullName() . ' [#' . $model->id . ']';
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-users"></i> ' . Yii::t('admin', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = '<i class="fa fa-user"></i> ' . $this->title;
?>

<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($model->isAllowUpdate()): ?>
            <?= Html::a(Yii::t('admin', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>&nbsp;
        <?php endif ?>

        <?php if ($model->isAllowDelete()): ?>
            <?= Html::a(Yii::t('admin', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('admin', 'Are you sure you want to delete this user?'),
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'hover' => true,
        'mode' => DetailView::MODE_VIEW,
        /*'panel' => [
            'heading' => 'Patient # ' . $model->id,
            'type' => DetailView::TYPE_INFO,
        ],*/
        'attributes' => [
            'id',
            'type' => [
                'attribute' => 'type',
                'value' => User::getTypeCaption($model->type),
                'format' => 'html'
            ],
            'email:email',
            'profession' => [
                'attribute' => 'profession',
                'value' => User::getProfessionCaption($model->profession),
                'format' => 'html'
            ],
            'companyName',
            'activity',
            'firstName',
            'lastName',
            'phone',
            'phoneMobile',
            'birthPlace',
            'address',
            'zip',
            'city',
            'comment:ntext',
            'logo' => [
                'attribute' => 'logo',
                'value' => $model->getLogoPath()
                    ? Html::a(Html::tag('div', Html::img($model->getLogoPath(true), ['class'=>'file-preview-image']), ['class' => 'file-preview-frame file-preview-initial']), \yii\helpers\Url::to($model->getLogoPath(true)), ['target' => '_blank'])
                    : '',
                'format' => 'raw'
            ],
            'isConfirmed:boolean',
            'isActive:boolean',
            [
                'attribute' => 'createdDate',
                'format' => ['datetime', 'php:d/m/Y H:i:s'],
            ],
            [
                'attribute' => 'updatedDate',
                'format' => ['datetime', 'php:d/m/Y H:i:s'],
            ],
            "targetSector",
            "targetAmount",
            "entryTicket",
            "turnoverName",
            "geographicalAreaName",
            "effective",
        ],
    ]) ?>

    <h2><?= Yii::t('app', 'History') ?></h2>
    <?= \common\extensions\arhistory\widgets\ARHistoryGridView::widget([
        'id' => 'user-history-grid',
        'table' => User::tableName(),
        'recordID' => $model->id,
        'filterModel' => null,
    ]); ?>

    <h2><?= Yii::t('app', 'Login history') ?></h2>
    <?= GridView::widget([
        'dataProvider' => $userHistory,
        'hover' => true,
        'condensed' => false,
        'striped' => true,
        'bordered' => true,
        'pjax' => false,
        'columns' => [
            [
                'attribute' => 'createdDate',
                'format' => ['date', 'php:d/m/Y H:i:s'],
            ],
            'eventLabel',
            'standartIp',
            'browser',
            'platform',
        ],
    ]); ?>
</div>
