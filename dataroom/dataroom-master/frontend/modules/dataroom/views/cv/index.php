<?php
    
use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\helpers\ArrayHelper;
?>

<div class="container">
    <!--<h1 class="page-header"><?/*= $this->title */?></h1>-->
    
    <?php $form = ActiveForm::begin([
        'enableClientValidation' => false,
        'validateOnSubmit' => false,
        'options' => ['class' => 'search-form horizontal-form'],
    ]); ?>

        <?= $form->field($searchModel, 'regionID')->widget(\kartik\widgets\Select2::class, [
            'data' => ArrayHelper::map(\common\models\Region::getList(), 'id', 'nameWithCode'),
            'options' => [
                'multiple' => true,
                'placeholder' => '',
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'tags' => false,
                'language' => [
                    'noResults' => new \yii\web\JsExpression('function() {
                        return "' . Yii::t('admin', 'No regions found.') . '";
                    }'),
                ],
            ],
            'pluginEvents' => [],
        ]); ?>

    <?= $form->field($searchModel, 'departmentID')->widget(\kartik\widgets\Select2::class, [
        'data' => ArrayHelper::map(\common\models\Department::getList(), 'id', 'nameWithCode'),
        'options' => [
            'multiple' => true,
            'placeholder' => '',
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => false,
            'language' => [
                'noResults' => new \yii\web\JsExpression('function() {
                    return "' . Yii::t('admin', 'No departments found.') . '";
                }'),
            ],
        ],
        'pluginEvents' => [],
    ]); ?>

    <?= $form->field($searchModel, 'activityDomainID')->widget(\kartik\widgets\Select2::class, [
        'data' => ArrayHelper::map(\backend\modules\dataroom\models\CVActivityDomain::getList(), 'id', 'name'),
        'options' => [
            'multiple' => true,
            'placeholder' => '',
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => false,
            'language' => [
                'noResults' => new \yii\web\JsExpression('function() {
                    return "' . Yii::t('admin', 'No domains found.') . '";
                }'),
            ],
        ],
        'pluginEvents' => [],
    ]); ?>

    <?= $form->field($searchModel, 'functionID')->widget(\kartik\widgets\Select2::class, [
        'data' => ArrayHelper::map(\backend\modules\dataroom\models\CVFunction::getList(null), 'id', 'name'),
        'options' => [
            'multiple' => true,
            'placeholder' => '',
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => false,
            'language' => [
                'noResults' => new \yii\web\JsExpression('function() {
                    return "' . Yii::t('admin', 'No functions found.') . '";
                }'),
            ],
        ],
        'pluginEvents' => [],
    ]); ?>

        <div class="form-group">
            <div class="col-md-6 col-md-offset-3">
                <?= Html::submitButton('Rechercher', ['class' => 'btn btn-primary pull-right']) ?>        
            </div>
        </div>

    <?php ActiveForm::end(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label' => 'N° de mandat',
                'format' => 'html',
                'value' => function($model) {
                    return Html::a($model->room->mandateNumber, ['cv/view-room', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'functionID',
                'value' => function($model) {
                    return $model->getFunctionName();
                }
            ],
            [
                'attribute' => 'candidateProfile',
                'format' => 'ntext'
            ],
            [
                'attribute' => 'activityDomainID',
                'value' => function($model) {
                    return $model->getActivityDomainName();
                }
            ],
            [
                'attribute' => 'regionID',
                'value' => function($model) {
                    return $model->getRegionName();
                }
            ],
            [
                'attribute' => 'publicationDate',
                'label' => 'Date de publication',
                'value' => function($model) {
                    return \common\helpers\DateHelper::getFrenchFormatDbDate($model->room->publicationDate);
                }
            ],
        ],
        'pager' => [
            'prevPageLabel' => Yii::t('app', 'Previous'),
            'nextPageLabel' => Yii::t('app', 'Next'),
        ],
    ]) ?>
</div>