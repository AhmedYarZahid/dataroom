<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Language */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '<i class="fa fa-language"></i> ' . Yii::t('admin', 'Languages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?php if ($model->isAllowDelete()): ?>
            <?= Html::a(Yii::t('admin', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('admin', 'Are you sure you want to delete this language?'),
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => Yii::t('admin', 'Icon'),
                'value' => $model->getIconHtml(),
                'format' => 'html'
            ],
            'locale',
            'name',
            'isDefault:boolean',
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
