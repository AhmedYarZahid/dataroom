<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\modules\staticpage\models\StaticPage;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\staticpage\models\StaticPageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model backend\modules\staticpage\models\StaticPage */

$this->title = Yii::t('staticpage', 'Static Pages');
$this->params['breadcrumbs'][] = '<i class="fa fa-file-text-o"></i> ' . $this->title;
?>
    <div class="staticpage-index">

        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a(Yii::t('admin', 'Create {modelClass}', [
                'modelClass' => Yii::t('admin', 'Static Page'),
            ]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?php //\yii\widgets\Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'title',
                [
                    'header' => Yii::t('staticpage', 'Type'),
                    'attribute' => 'type',
                    'filter' => StaticPage::getTypes(),
                    'value' => function($model, $key, $index, $column) {
                        return StaticPage::getTypeCaption($model->type);
                    },
                    'options' => ['style' => 'width:150px;'],
                    'hAlign' => 'center'
                ],
                [
                    'header' => Yii::t('staticpage', 'Link'),
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        return Html::button(Yii::t('admin', 'Copy'), ["class" => "btn btn-primary btn-xs copyToClipboard", "data-page-link" => $model->getPageLink()]);
                    },
                    'options' => ['style' => 'width:50px;']
                ],
                [
                    'class' => '\kartik\grid\ActionColumn',
                    'template' => '{update}',
                    /*'buttons' => [
                        'update' => function ($url, $model, $key) {
                            return !$model->isDefault ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => Yii::t('notify', 'Delete'), 'data' => [
                                'confirm' => Yii::t('admin', 'Are you sure you want to delete this notification?'),
                                'method' => 'post',
                            ]]) : '';
                        }
                    ],*/
                    'options' => ['style' => 'width: 65px;']
                ],
            ],
        ]); ?>
        <?php //\yii\widgets\Pjax::end(); ?>
    </div>

<?php $this->registerJs("
    $('body').on('click', '.copyToClipboard', function() {
        window.prompt('Appuyez sur \"Ctrl + C\" et puis cliquez sur \"Ok\" ou appuyez \"Entrée\".', $(this).data('pageLink'));
    });
"); ?>