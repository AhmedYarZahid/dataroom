<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model backend\modules\metatags\models\MetaTagsAttrs */

$this->title = Yii::t('metatags', 'Meta Tags Attributes');
$this->params['breadcrumbs'][] = '<i class="fa fa-file-code-o"></i> ' . $this->title;
?>
    <div class="metatags-attributes-index">

        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a(Yii::t('admin', 'Create {modelClass}', [
                'modelClass' => Yii::t('metatags', 'Meta Tags'),
            ]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?php //\yii\widgets\Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'attrName',
                [
                    'class' => '\kartik\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'options' => ['style' => 'width: 65px;']
                ],
            ],
        ]); ?>
        <?php //\yii\widgets\Pjax::end(); ?>
    </div>