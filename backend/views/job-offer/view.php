<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\JobOffer */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-slideshare"></i>' . Yii::t('admin', 'Job Offers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="job-offer-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('admin', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'location',
            'description:ntext',
            'skills:ntext',
            'contractType',
            [
                'attribute' => 'salary',
            ],
            'contactEmail:email',
            [
                'attribute' => 'startDate',
                'format' => ['datetime', 'php:d/m/Y'],
            ],
            [
                'attribute' => 'expiryDate',
                'format' => ['datetime', 'php:d/m/Y'],
            ],
            [
                'attribute' => 'publicationDate',
                'format' => ['datetime', 'php:d/m/Y H:i:s'],
            ],
            [
                'attribute' => 'createdDate',
                'format' => ['datetime', 'php:d/m/Y H:i:s'],
            ],
            [
                'attribute' => 'updatedDate',
                'format' => ['datetime', 'php:d/m/Y H:i:s'],
            ]
        ],
    ]) ?>

</div>
