<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LanguageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Languages');
$this->params['breadcrumbs'][] = '<i class="fa fa-language"></i> ' . $this->title;
?>
<div class="language-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Add Language'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'format' => 'html',
                'contentOptions' => ['style' => 'font-weight:bold;'],
                'options' => ['style' => 'width:100px;'],
            ],
            [
                'header' => Yii::t('admin', 'Icon'),
                'value' => function ($model) {
                    return $model->getIconHtml();
                },
                'format' => 'html',
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'attribute' => 'name',
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'attribute' => 'locale',
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'class' => '\kartik\grid\BooleanColumn',
                'attribute' => 'isDefault',
                'trueLabel' => Yii::t('app', 'Yes'),
                'falseLabel' => Yii::t('app', 'No'),
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        return $model->isAllowDelete() ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => Yii::t('admin', 'Delete'), 'data' => [
                            'confirm' => Yii::t('admin', 'Are you sure you want to delete this language?'),
                            'method' => 'post',
                        ]]) : '';
                    }
                ],
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'options' => ['style' => 'width: 65px;']
            ],
        ],
    ]); ?>

</div>
