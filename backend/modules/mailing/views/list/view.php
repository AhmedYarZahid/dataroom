<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\datecontrol\DateControl;
use common\helpers\DateHelper;

/* @var $this yii\web\View */
/* @var $model backend\modules\mailing\models\MailingList */

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-send-o"></i> ' . Yii::t('admin', 'Mailing lists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="mailing-list-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'id' => 'mailing-list-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => '\kartik\grid\CheckboxColumn'
            ],
            'id',
            [
                'attribute' => 'email',
            ],
            [
                'attribute' => 'type',
                'filter' => \backend\modules\mailing\models\MailingContactForm::getProfileList(),
                'value' => function ($model) {
                    return \backend\modules\mailing\models\MailingContactForm::getProfileCaption($model['type']);
                }
            ],
            [
                'attribute' => 'fullName',
                'label' => Yii::t('admin', 'Name'),
            ],
            [
                'class' => '\kartik\grid\BooleanColumn',
                'attribute' => 'isActive',
                'label' => Yii::t('admin', 'Is Active'),
            ],
            [
                'class' => '\kartik\grid\ActionColumn',
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model, $key) use ($listID) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete-contact', 'id' => $model['id'], 'listID' => $listID], ['title' => Yii::t('admin', 'Delete'), 'data' => [
                            'confirm' => Yii::t('admin', 'Are you sure you want to delete this user from the contact list?'),
                            'method' => 'post',
                        ]]);
                    },
                ],
                //'deleteOptions' => ['message' => 'Custom message'],
                'options' => ['style' => 'width: 50px;']
            ],
        ],
    ]); ?>

    <?= Html::a(Yii::t('admin', 'Delete selected contacts'), 'javascript:void(0);', ['class' => 'btn btn-danger', 'id' => 'delete-contacts-link']) ?>

</div>

<?php $this->registerJs('
$("body").on("click", "#delete-contacts-link", function(event) {
    contactIDs = $("#mailing-list-grid").yiiGridView("getSelectedRows");
    if (!contactIDs.length) {
        return;
    }

    $.ajax({
        url: "' . \yii\helpers\Url::to(['delete-contacts']) . '",
        data: {"ids": contactIDs},
        type: "GET",
        dataType: "json",
        success: function(data) {
            $("#mailing-list-grid").yiiGridView("applyFilter");
        }
    });
});
') ?>