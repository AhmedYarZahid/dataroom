<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\modules\contact\models\Contact;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\contact\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('contact', 'Contact');
$this->params['breadcrumbs'][] = '<i class="fa fa-comments-o"></i> ' . $this->title;
?>
    <div id="contact-index">

        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a(Yii::t('contact', 'Manage templates'), ['manage-template/index'], ['class' => 'btn btn-primary btn-xs']) ?>
        </p>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'id',
                    'options' => ['style' => 'width:70px;'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle'
                ],
                [
                    'attribute' => 'type',
                    'value' => function ($model) {
                        return Contact::getTypeCaption($model->type);
                    },
                    'filter' => Contact::getTypes(),
                    'options' => ['style' => 'width:170px;'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle'
                ],
                [
                    'label' => Yii::t('contact', 'Subject'),
                    'attribute' => 'subject',
                    'format' => 'raw',
                    'value' => function($model, $key) {
                        return ($model->hasNewMessage ? '<span class="label label-warning pull-left bg-yellow">new</span>' : '')
                            . $model->subject
                            . ' '
                            . Html::button('<span class="glyphicon glyphicon-search"></span>', [
                            'class' => 'contact-preview-link btn-link',
                            'data-pjax' => '0',
                            //'title' => Yii::t('yii', 'View contact thread'),
                        ]);
                    },
                    'options' => ['style' => 'width:300px;'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle'
                ],
                [
                    'attribute' => 'firstName',
                    'options' => ['style' => 'width:150px;'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle'
                ],
                [
                    'attribute' => 'lastName',
                    'options' => ['style' => 'width:150px;'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle'
                ],
                [
                    'attribute' => 'email',
                    'format' => 'email',
                    'options' => ['style' => 'width:150px;'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle'
                ],
                [
                    'attribute' => 'phone',
                    'options' => ['style' => 'width:150px;'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle'
                ],
                [
                    'attribute' => 'responsesNumber',
                    'options' => ['style' => 'width:100px;'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle'
                ],
                [
                    'class' => '\kartik\grid\BooleanColumn',
                    'label' => Yii::t('contact', 'Opened'),
                    'attribute' => 'isClosed',
                    'value' => function($model) {
                        return !$model->isClosed;
                    },
                    'trueLabel' => Yii::t('contact', 'Closed'),
                    'falseLabel' => Yii::t('contact', 'Opened'),
                    'options' => ['style' => 'width:100px;'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle'
                ],
                [
                    'attribute' => 'createdDate',
                    'format' => ['date', 'php:d/m/Y'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'template' => '{view} {toggle-close}',
                    'buttons' => [
                        'toggle-close' => function ($url, $model, $key) {
                            return !$model->isClosed
                                ? Html::a('<span class="fa fa-lock"></span>', $url, ['title' => Yii::t('notify', 'Close thread'), 'data' => [
                                    'confirm' => Yii::t('admin', 'Are you sure you want to close this thread?'),
                                    'method' => 'post',
                                ]])
                                : Html::a('<span class="fa fa-unlock"></span>', $url, ['title' => Yii::t('notify', 'Open thread'), 'data' => [
                                    'confirm' => Yii::t('admin', 'Are you sure you want to open this thread?'),
                                    'method' => 'post',
                                ]]);
                        }
                    ],
                    'options' => ['style' => 'width: 65px;'],
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                ],
            ],
        ]); ?>
    </div>


<?php $this->registerJs("
    $('body').on('click', '.contact-preview-link', function(event) {

        var that = this;

        $('[rel=popover]').not(this).popover('hide');

        if ($(this).next('div.popover').length || $(this).attr('rel') == 'popover') {
            $(this).popover('toggle');
        } else {
            $.get('preview', { id: $(this).closest('tr').data('key') },
                function (data) {
                    $(that).data('content', data);
                    $(that).attr('rel', 'popover');

                    $(that).popover({trigger: 'manual', html: true});
                    $(that).popover('toggle');
                }
            );
        }
    });

    // Hide popover by clicking outside
    /*$('body').on('click', function (e) {
        $('.contact-thread-view-link').popover('hide');
    });*/
 "); ?>