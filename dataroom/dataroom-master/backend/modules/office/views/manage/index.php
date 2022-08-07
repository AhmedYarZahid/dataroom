<?php

use yii\helpers\Html;
use kartik\grid\GridView;

$this->title = Yii::t('admin', 'Offices');
$this->params['breadcrumbs'][] = '<i class="fa fa-file-text-o"></i> ' . $this->title;
?>
 
<div class="office-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Create new office'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('admin', 'Manage cities'), ['manage-cities/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin', 'Manage members'), ['manage-members/index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            [
                'attribute' => 'cityID',
                'value' => function($model) {
                    return $model->city->name;
                }
            ],
            [
                'class' => '\kartik\grid\BooleanColumn',
                'attribute' => 'isActive',
            ],

            [
                'class' => '\kartik\grid\ActionColumn',
                'template' => '{update} {delete}',
                'deleteOptions' => ['message' => Yii::t('admin', 'Are you sure you want to delete this office?')],
                'options' => ['style' => 'width: 65px;'],
            ],
        ],
    ]); ?>

</div>