<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\modules\parameter\models\Parameter;
use \kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\parameter\models\ParameterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model backend\modules\parameter\models\Parameter */

$this->title = Yii::t('notify', 'Parameters');
$this->params['breadcrumbs'][] = '<i class="fa fa-wrench"></i> ' . $this->title;
?>
<div class="staticpage-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php //\yii\widgets\Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'description',
                'options' => ['style' => 'width:65%;']
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'header' => Yii::t('parameter', 'Value'),
                'attribute' => 'value',
                'refreshGrid' => true,
                'editableOptions' => function ($model, $key, $index) {
                    if ($model->type == "boolean") {
                        return [
                            'inputType' => \kartik\editable\Editable::INPUT_CHECKBOX,
                            'options' => ['label' => Yii::t('parameter', 'On/Off')],
                            'placement' => \kartik\popover\PopoverX::ALIGN_TOP,
                            'size' => 'sm'
                        ];
                    } else {
                        return [
                            'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                            'placement' => \kartik\popover\PopoverX::ALIGN_TOP,
                            'size' => \kartik\popover\PopoverX::SIZE_MEDIUM
                        ];
                    }
                },
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'width' => '100px',
                'format' => 'raw',
                'options' => ['style' => 'width:20%;'],
            ],
            [
                'attribute' => 'updatedDate',
                'format' => ['datetime', 'php:d/m/Y H:i:s'],
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],
        ],
    ]); ?>
    <?php //\yii\widgets\Pjax::end(); ?>
</div>