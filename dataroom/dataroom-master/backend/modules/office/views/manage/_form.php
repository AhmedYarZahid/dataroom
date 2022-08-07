<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use voime\GoogleMaps\MapInput;

?>

<div class="office-form">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">

                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false,
                ]); ?>

                <div class="box-body">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'body')->textArea(['rows' => 5]) ?>
                    <?= $form->field($model, 'cityID')->dropDownList($model->cityList(), ['prompt' => '']) ?>
                    <?= $form->field($model, 'isActive')->checkbox() ?>
                    
                    <?= $form->field($model, 'address')->textInput(['id'=>'address-input', 'placeholder' => '']) ?>

                    <?php
                    echo MapInput::widget([
                        'center' => 'Paris',
                        'height' => '400px',
                        'width' => '100%',
                        'zoom' => 12,
                        //'zoom' => 15,
                        'mapOptions' => [
                            'maxZoom' => '15',
                        ],
                    ]);
                    ?>
                    <?=$form->field($model, 'latitude')->hiddenInput(['id'=>'lat-input'])->label(false) ?>
                    <?=$form->field($model, 'longitude')->hiddenInput(['id'=>'lng-input'])->label(false) ?>
                </div>

                <div class="box-footer">
                    <div class="form-group">
                        <?= Html::submitButton($model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>