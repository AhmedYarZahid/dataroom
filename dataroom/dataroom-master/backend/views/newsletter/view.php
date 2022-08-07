<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Newsletter */

$this->title = $model->email;
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-newspaper-o"></i>' . Yii::t('admin', 'Newsletter Subscribers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="newsletter-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('admin', 'Are you sure you want to delete this subscription?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'email:email',
            'firstName',
            'lastName',
            [
                'attribute' => 'profession',
                'value' => $model->professionCaption(),
            ],
            [
                'attribute' => 'createdDate',
                'format' => ['datetime', 'php:d/m/Y H:i:s'],
            ],
        ],
    ]) ?>

</div>
