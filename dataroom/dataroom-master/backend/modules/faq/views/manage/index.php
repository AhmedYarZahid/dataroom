<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\faq\models\FaqItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model backend\modules\faq\models\FaqItem */
/* @var $category backend\modules\faq\models\FaqCategory */

$this->title = Yii::t('app', 'FAQ');
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-question-circle-o"></i> FAQ', 'url' => '../category/index'];
?>

<div class="faq-items-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Create FAQ record'), ['create', 'faqCategoryID' => $category->id], ['class' => 'btn btn-success']) ?>
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
            'question',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'options' => ['style' => 'width: 50px;']
            ],
        ],
    ]); ?>

</div>