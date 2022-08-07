<?php

use yii\helpers\Html;
use kartik\grid\GridView;

$this->title = Yii::t('admin', 'Cities');
$this->params['breadcrumbs'][] = '<i class="fa fa-file-text-o"></i> ' . $this->title;
?>
 
<div class="city-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Create {modelClass}', [
            'modelClass' => Yii::t('admin', 'City'),
        ]), ['create'], ['class' => 'btn btn-success']) ?>

        <?= Html::a(Yii::t('admin', 'Manage offices'), ['manage/index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            [
                'class' => '\kartik\grid\BooleanColumn',
                'attribute' => 'isActive',
            ],

            [
                'class' => '\kartik\grid\ActionColumn',
                'template' => '{update} {delete}',
                'deleteOptions' => ['message' => Yii::t('admin', 'Are you sure you want to delete this city?')],
                'options' => ['style' => 'width: 65px;'],
            ],
        ],
    ]); ?>
    
    <?= $this->render('_map', ['markers' => $markers]) ?>
</div>