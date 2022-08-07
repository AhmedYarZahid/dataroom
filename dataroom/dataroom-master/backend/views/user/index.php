<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Users');
$this->params['breadcrumbs'][] = '<i class="fa fa-users"></i> ' . $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Create {modelClass}', [
            'modelClass' => Yii::t('admin', 'User'),
        ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            /*['class' => 'yii\grid\SerialColumn'],*/
            'id',
            'email:email',
            [
                'attribute' => 'type',
                'filter' => User::getTypes(),
                'value' => function ($model) {
                    return User::getTypeCaption($model->type);
                },
                'options' => ['style' => 'width:200px;'],
            ],
            [
                'attribute' => 'profession',
                'filter' => User::getProfessions(),
                'value' => function ($model) {
                    return User::getProfessionCaption($model->profession);
                },
            ],
            'firstName',
            'lastName',
            //'phoneMobile',
            [
                'class' => '\kartik\grid\BooleanColumn',
                'attribute' => 'isActive'
                //'trueLabel' => 'Yes',
                //'falseLabel' => 'No'
            ],
            // 'address',
            // 'zip',
            // 'logo',
            // 'tempEmail:email',
            // 'isConfirmed',
            // 'createdDate',
            // 'updatedDate',

            [
                'class' => '\kartik\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return $model->isAllowUpdate() ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => Yii::t('admin', 'Update'), 'data-pjax' => 0]) : '';
                    },
                    'delete' => function ($url, $model, $key) {
                        return $model->isAllowDelete() ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => Yii::t('admin', 'Delete'), 'data' => [
                            'confirm' => Yii::t('admin', 'Are you sure you want to delete this user?'),
                            'method' => 'post',
                        ]]) : '';
                    }
                ],
                //'deleteOptions' => ['message' => 'Custom message'],
                'options' => ['style' => 'width: 65px;']
            ],
        ],
    ]); ?>

</div>
