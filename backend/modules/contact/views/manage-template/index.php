<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\modules\contact\models\Contact;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\contact\models\ContactTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model backend\modules\contact\models\Contact */

$this->title = Yii::t('staticpage', 'Contact Templates');
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-comments-o"></i> ' . Yii::t('contact', 'Contact'), 'url' => ['manage/index']];
$this->params['breadcrumbs'][] = '<i class="fa fa-file-code-o"></i> ' . $this->title;
?>
<div class="contact-templates-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Create {modelClass}', [
            'modelClass' => Yii::t('admin', 'Template'),
        ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php //\yii\widgets\Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'options' => ['style' => 'width:100px;'],
                'hAlign' => 'center',
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'name',
                'options' => ['style' => 'width:150px;'],
                'hAlign' => 'center',
                'vAlign' => 'middle'
            ],
            [
                'attribute' => 'body',
                'format' => 'html'
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{update} {delete}',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'options' => ['style' => 'width: 65px;']
            ],
        ],
    ]); ?>
    <?php //\yii\widgets\Pjax::end(); ?>
</div>