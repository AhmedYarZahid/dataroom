<?php

use backend\modules\dataroom\models\ProfileCompany;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use common\models\User;
use backend\modules\dataroom\Module as DataroomModule;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
/* @var $photosInitData array */
?>

<div class="user-form">
    <div class="row">
        <div class="col-md-8">
            <div class="box box-primary">

                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false,
                    'options' => ['enctype' => 'multipart/form-data'],

                ]); ?>

                <div class="box-header">
                    <h3 class="box-title"><?= Yii::t('admin', 'User Data') ?></h3>
                </div>

                <div class="box-body">

                    <?php if (!$model->isNewRecord): ?>
                        <?= $form->field($model, 'type')->hiddenInput()->label(Yii::t('admin', 'Type: ' . User::getTypeCaption($model->type))); ?>
                    <?php else: ?>
                        <?= $form->field($model, 'type')->dropDownList(User::getTypes([User::TYPE_SUPERADMIN]), ['prompt' => '']) ?>
                    <?php endif ?>

                    <?= $form->field($model, 'email')->textInput(['maxlength' => 150]) ?>

                    <?= $form->field($model, 'profession')->dropDownList(User::getProfessions([], true), ['prompt' => '', 'disabled' => (!$model->isNewRecord && $model->type == User::TYPE_ADMIN)]) ?>

                    <?= $form->field($model, 'companyName')->textInput(['maxlength' => 70]) ?>

                    <?= $form->field($model, 'activity')->textInput(['maxlength' => 70]) ?>

                    <?= $form->field($model, 'firstName')->textInput(['maxlength' => 50]) ?>

                    <?= $form->field($model, 'lastName')->textInput(['maxlength' => 50]) ?>

                    <?php if ($model->isNewRecord): ?>
                        <?= $form->field($model, 'password')->passwordInput() ?>
                        <?= $form->field($model, 'passwordConfirm')->passwordInput() ?>
                    <?php endif ?>

                    <?= $form->field($model, 'phone')->textInput(['maxlength' => 30]) ?>
                    
                    <?= $form->field($model, 'phoneMobile')->textInput(['maxlength' => 30]) ?>
                    
                    <?= $form->field($model, 'birthPlace')->textInput(['maxlength' => 150]) ?>
                    <?= $form->field($model, 'address')->textInput(['maxlength' => 250]) ?>

                    <?= $form->field($model, 'zip')->textInput(['maxlength' => 5]) ?>

                    <?= $form->field($model, 'city')->textInput(['maxlength' => 150]) ?>

                    <?= $form->field($model, 'targetedSector')->widget(Select2::class, [
                        'data' => ProfileCompany::sectorList(),
                        'options' => [
                            'multiple' => true,
                            'placeholder' => ''
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'tags' => false,
                            'language' => [
                                'noResults' => new JsExpression('function() {
                                                return "' . Yii::t('admin', 'No targeted sector found.') . '";
                                            }'),
                            ],
                        ],
                        'pluginEvents' => [],
                    ]); ?>
                    <?= $form->field($model, 'targetedTurnover')->widget(Select2::class, [
                        'data' => ProfileCompany::turnoverList(),
                        'options' => [
                            'multiple' => true,
                            'placeholder' => ''
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'tags' => false,
                            'language' => [
                                'noResults' => new JsExpression('function() {
                                                return "' . Yii::t('admin', 'No target turnover found.') . '";
                                            }'),
                            ],
                        ],
                        'pluginEvents' => [],
                    ]); ?>
                    <?= $form->field($model, 'entranceTicket')->widget(Select2::class, [
                        'data' => ProfileCompany::ticketList(),
                        'options' => [
                            'multiple' => true,
                            'placeholder' => ''
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'tags' => false,
                            'language' => [
                                'noResults' => new JsExpression('function() {
                                                return "' . Yii::t('admin', 'No entrance ticket found.') . '";
                                            }'),
                            ],
                        ],
                        'pluginEvents' => [],
                    ]); ?>
                    <?= $form->field($model, 'geographicalArea')->widget(Select2::class, [
                        'data' => ProfileCompany::getGeographicalAreaList(),
                        'options' => [
                            'multiple' => true,
                            'placeholder' => ''
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'tags' => false,
                            'language' => [
                                'noResults' => new JsExpression('function() {
                                                return "' . Yii::t('admin', 'No geographical area found.') . '";
                                            }'),
                            ],
                        ],
                        'pluginEvents' => [],
                    ]); ?>
                    <?= $form->field($model, 'targetAmount')->widget(Select2::class, [
                        'data' => ProfileCompany::getTargetAmountList(),
                        'options' => [
                            'multiple' => true,
                            'placeholder' => ''
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'tags' => false,
                            'language' => [
                                'noResults' => new JsExpression('function() {
                                                return "' . Yii::t('admin', 'No target amount found.') . '";
                                            }'),
                            ],
                        ],
                        'pluginEvents' => [],
                    ]); ?>
                    <div class="form-group field-mailingcontactform-effective">
                        <label class="control-label" for="mailingcontactform-effective">Effectif</label>
                        <?= Yii::t('admin', 'between') . '&nbsp;&nbsp;' . Html::activeTextInput($model, 'effectiveMin', ['style' => 'width: 100px;']); ?>
                        &nbsp;
                        <?= Yii::t('admin', 'and') . '&nbsp;&nbsp;' . Html::activeTextInput($model, 'effectiveMax', ['style' => 'width: 100px;']); ?>
                    </div>

                    <?= $form->field($model, 'comment')->textarea() ?>

                    <?= $form->field($model, 'isMailingContact')->checkbox() ?>

                    <?php foreach ($profiles as $section => $profile): ?>

                        <?php if ($section == DataroomModule::SECTION_COMPANIES): ?>

                            <div class="section-fields section-companies">
                                <h4>Champs à remplir pour recevoir nos actualités AJArepreneurs</h4>
                                <?= $form->field($profiles[$section], 'targetedSector')->dropDownList($profiles[$section]::sectorList(), ['prompt' => '']) ?>
                                <?= $form->field($profiles[$section], 'targetedTurnover')->dropDownList($profiles[$section]::turnoverList(), ['prompt' => '']) ?>
                                <?= $form->field($profiles[$section], 'entranceTicket')->dropDownList($profiles[$section]::ticketList(), ['prompt' => '']) ?>

                                <?= $form->field($profiles[$section], 'geographicalArea', ['enableClientValidation' => false])->dropDownList($profiles[$section]::getGeographicalAreaList(), ['prompt' => '']) ?>
                                <?= $form->field($profiles[$section], 'targetAmount', ['enableClientValidation' => false])->dropDownList($profiles[$section]::getTargetAmountList(), ['prompt' => '']) ?>
                                <?= $form->field($profiles[$section], 'effective', ['enableClientValidation' => false])->textInput() ?>
                            </div>

                        <?php elseif ($section == DataroomModule::SECTION_REAL_ESTATE): ?>

                            <div class="section-fields section-real-estate">
                                <h4>Champs à remplir pour recevoir nos actualités AJAimmo</h4>
                                <?= $form->field($profiles[$section], 'targetSector')->dropDownList($profiles[$section]::getTargetSectors(), ['prompt' => '']) ?>

                                <?= $form->field($profiles[$section], 'regionIDs')->widget(Select2::class, [
                                    'data' => ArrayHelper::map(\common\models\Region::find()->all(), 'id', 'nameWithCode'),
                                    'options' => [
                                        'multiple' => true,
                                        'placeholder' => Yii::t('admin', 'Start typing region name'),
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'tags' => false,
                                        'language' => [
                                            'noResults' => new JsExpression('function() {
                                            return "' . Yii::t('admin', 'No regions found.') . '";
                                        }'),
                                        ],
                                    ],
                                    'pluginEvents' => [],
                                ]); ?>

                                <?= $form->field($profiles[$section], 'targetedAssetsAmount')->dropDownList($profiles[$section]::getTargetedAssetsAmountList(), ['prompt' => '']) ?>
                                <?= $form->field($profiles[$section], 'assetsDestination')->dropDownList($profiles[$section]::getAssetsDestinationList(), ['prompt' => '']) ?>
                                <?= $form->field($profiles[$section], 'operationNature')->dropDownList($profiles[$section]::getOperationNatureList(), ['prompt' => '']) ?>
                            </div>

                         <?php elseif ($section == DataroomModule::SECTION_COOWNERSHIP): ?>

                            <div class="section-fields section-coownership">
                                <h4>Champs à remplir pour recevoir nos actualités AJAsyndic</h4>
                                <?= $form->field($profiles[$section], 'propertyType')->dropDownList(\backend\modules\dataroom\models\RoomCoownership::getPropertyTypes(), ['prompt' => '']) ?>

                                <?= $form->field($profiles[$section], 'regionIDs')->widget(Select2::class, [
                                    'data' => ArrayHelper::map(\common\models\Region::find()->all(), 'id', 'nameWithCode'),
                                    'options' => [
                                        'multiple' => true,
                                        'placeholder' => Yii::t('admin', 'Start typing region name'),
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'tags' => false,
                                        'language' => [
                                            'noResults' => new JsExpression('function() {
                                            return "' . Yii::t('admin', 'No regions found.') . '";
                                        }'),
                                        ],
                                    ],
                                    'pluginEvents' => [],
                                ]); ?>

                                <?= $form->field($profiles[$section], 'lotsNumber')->textInput() ?>
                                <?= $form->field($profiles[$section], 'coownersNumber')->textInput() ?>
                            </div>

                        <?php endif ?>
                    <?php endforeach ?>

                    <?= $form->field($model, 'isActive')->checkbox() ?>

                    <?= \common\extensions\arhistory\widgets\ARHistoryCommentField::widget([
                        'form' => $form,
                        'model' => $model,
                    ]); ?>

                </div>

                <div class="box-footer">
                    <div class="form-group">
                        <?= Html::submitButton($model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>

        <?php if (!$model->isNewRecord): ?>
            <div class="col-md-4">
                <div class="box box-danger">

                    <?php $form = ActiveForm::begin([
                        'enableClientValidation' => false,
                        'validateOnSubmit' => false,
                    ]); ?>

                    <div class="box-header">
                        <h3 class="box-title"><?= Yii::t('admin', 'Update Password') ?></h3>
                    </div>

                    <div class="box-body">
                        <?= $form->field($model, 'password')->passwordInput() ?>
                        <?= $form->field($model, 'passwordConfirm')->passwordInput() ?>
                    </div>

                    <div class="box-footer">
                        <div class="form-group">
                            <?= Html::submitButton(Yii::t('admin', 'Update Password'), ['class' => 'btn btn-primary', 'name' => 'UpdatePassword']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>
        <?php endif ?>

    </div>
</div>

<?php $this->registerJs("
$('body').on('change', '#user-type', function() {
    if ($(this).val() == '" . User::TYPE_ADMIN . "') {
        $('#user-profession').val(" . \common\models\Newsletter::PROFESSION_MEMBER_AJA . ").prop('disabled', true);
    } else {
        $('#user-profession').val('').prop('disabled', false);
    }
});
"); ?>