<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\JobOfferSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Job Offers');
$this->params['breadcrumbs'][] = '<i class="fa fa-slideshare"></i> ' . $this->title;
?>
<div class="job-offer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Add Job Offer'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'options' => ['style' => 'width:100px;'],
            ],
            [
                'attribute' => 'title',
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'attribute' => 'contractType',
                /*'value' => function (\common\models\JobOffer $model) {
                    return $model->getContractTypeCaption($model->contractType);
                },
                'filter' => \common\models\JobOffer::getContractTypes(),*/
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'attribute' => 'salary',
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'attribute' => 'contactEmail',
                'format' => 'email',
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
            [
                'attribute' => 'expiryDate',
                'format' => ['date', 'php:d/m/Y'],
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                        'todayHighlight' => true,
                    ],
                ],
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'options' => ['style' => 'width:200px;'],
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'options' => ['style' => 'width: 65px;']
            ],
        ],
    ]); ?>

</div>
