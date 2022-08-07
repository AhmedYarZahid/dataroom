<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use lateos\trendypage\models\TrendyPage;
use lateos\trendypage\ManageIndexAsset;

/* @var $this yii\web\View */
/* @var $searchModel lateos\trendypage\models\TrendyPageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model lateos\trendypage\models\TrendyPage */

$this->title = Yii::t('trendypage', 'Trendy Pages');
$this->params['breadcrumbs'][] = '<i class="fa fa-file-text-o"></i> ' . $this->title;

ManageIndexAsset::register($this);
?>
    <div class="trendypage-index">

        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a(Yii::t('trendypage', 'Create {modelClass}', [
                'modelClass' => Yii::t('trendypage', 'Trendy Page'),
            ]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?php //\yii\widgets\Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'hover' => true,
            'condensed' => false,
            'striped' => true,
            'bordered' => true,
            'pjax' => true,
            'columns' => [
                [
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        return Html::checkbox('pagesToDelete[]', false, [
                            'value' => $model->id,
                        ]);
                    },
                    'options' => ['style' => 'width:50px;']
                ],
                'title',
                [
                    'header' => Yii::t('trendypage', 'Link'),
                    'format' => 'raw',
                    'value' => function ($model, $key, $index, $column) {
                        return Html::button(Yii::t('trendypage', 'Copy'), ["class" => "btn btn-primary btn-xs copyToClipboard", "data-page-link" => $model->getPageLink()]);
                    },
                    'options' => ['style' => 'width:50px;']
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete} {clone}',
                    'buttons' => [
                        'clone' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-duplicate"></span>', $url, ['title' => Yii::t('trendypage', 'Copy page'), 'data' => [
                                'confirm' => Yii::t('trendypage', 'Are you sure you want to copy this page ?'),
                                'method' => 'post',
                            ]]);
                        }
                    ],
                    'visibleButtons' => [
                        'delete' => function($model, $key, $index) {
                            return $model->id != 1;
                        }
                    ],
                    'options' => ['style' => 'width: 70px;']
                ],
            ],
        ]); ?>
        <?php //\yii\widgets\Pjax::end(); ?>

        <?php $form = ActiveForm::begin([
            'action' => Url::to(['manage/delete-multiple']),
            'enableClientValidation' => false,
            'validateOnSubmit' => false,
            'options' => [
                'class' => 'delete-selected hidden',
                'data-confirm-message' => Yii::t('trendypage', 'Are you sure you want to delete selected pages?'),
            ],
        ]); ?>

            <?= Html::button(Yii::t('trendypage', 'Delete selected'), ['class' => 'delete-selected-btn btn btn-danger']) ?>

        <?php ActiveForm::end(); ?>
    </div>

<?php $this->registerJs("
    $('body').on('click', '.copyToClipboard', function() {
        window.prompt('Appuyez sur \"Ctrl + C\" et puis cliquez sur \"Ok\" ou appuyez \"Entrée\".', $(this).data('pageLink'));
    });
"); ?>