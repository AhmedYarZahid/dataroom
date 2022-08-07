<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Menu */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-sitemap"></i>' . Yii::t('admin', 'Public Menu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('admin', 'Are you sure you want to delete this menu item? All child items will be removed also!'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            [
                'attribute' => 'parentTitle',
                'value' => $model->parent ? Html::a($model->parentTitle, ['view', 'id' => $model->parent->id]) : '-',
                'format' => 'html'
            ],
            [
                'attribute' => 'entity',
                'value' => \common\models\Menu::getEntityCaption($model->entity),
            ],
            [
                'attribute' => 'url',
                'value' => \kartik\helpers\Html::a($model->getItemUrl(), $model->getItemUrl()),
                'format' => 'raw'
            ],
            'rank',
            'isActive:boolean',
            [
                'attribute' => 'createdDate',
                'format' => ['datetime', 'php:d/m/Y H:i:s'],
            ],
            [
                'attribute' => 'updatedDate',
                'format' => ['datetime', 'php:d/m/Y H:i:s'],
            ]
        ],
    ]) ?>

</div>
