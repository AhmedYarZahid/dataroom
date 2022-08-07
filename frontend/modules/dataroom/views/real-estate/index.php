<?php
    
use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<div class="container">
    <!--<h1 class="page-header"><?/*= $this->title */?></h1>-->
    
    <?php $form = ActiveForm::begin([
        'enableClientValidation' => false,
        'validateOnSubmit' => false,
        'options' => ['class' => 'search-form horizontal-form'],
    ]); ?>

        <?= $form->field($searchModel, 'zip')->textInput() ?>
        <?= $form->field($searchModel, 'propertyType', ['options' => ['style' => 'width:650px;']])->widget(\kartik\widgets\Select2::class, [
            'data' => \backend\modules\dataroom\models\RoomRealEstate::getPropertyTypes(),
            'options' => [
                'multiple' => true,
                'placeholder' => '', //Yii::t('admin', 'Start typing property type'),
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'tags' => false,
                'language' => [
                    'noResults' => new \yii\web\JsExpression('function() {
                        return "' . Yii::t('admin', 'No property types found.') . '";
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
                    return Html::a($model->room->mandateNumber, ['real-estate/view-room', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'libAd',
            ],
            [
                'attribute' => 'propertyType',
                'value' => function($model) {
                    return \backend\modules\dataroom\models\RoomRealEstate::getPropertyTypeCaption($model->propertyType);
                }
            ],
            [
                'attribute' => 'zip',
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