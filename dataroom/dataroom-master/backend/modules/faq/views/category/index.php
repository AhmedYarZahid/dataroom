<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use backend\modules\faq\models\FaqCategory;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\trendypage\models\TrendyPageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model backend\modules\faq\models\FaqCategory */

$this->title = Yii::t('app', 'FAQ Categories');
$this->params['breadcrumbs'][] = '<i class="fa fa-question-circle-o"></i> ' . $this->title;
?>

<div class="faq-categories-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Create FAQ category'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'hover' => true,
        'condensed' => false,
        'striped' => true,
        'bordered' => true,
        'pjax' => false,
        'columns' => [
            [
                'attribute' => 'title',
                'value' => function (FaqCategory $data) {
                    return Html::a(Html::encode($data->title), Url::to(['manage/index', 'faqCategoryID' => $data->id]));
                },
                'format' => 'raw',
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['manage/index', 'faqCategoryID' => $model->id], ['title' => Yii::t('admin', 'View')]);
                    }
                ],
                'options' => ['style' => 'width: 50px;']
            ],
        ],
    ]); ?>

</div>
