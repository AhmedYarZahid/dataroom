<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\modules\dataroom\Module as DataroomModule;
use yii\helpers\ArrayHelper;

$this->title = 'Votre profil';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="my-profile container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="content-header col-md-6">
            <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3"></div>
        <div class="profile-form col-md-6">
            <?php $form = ActiveForm::begin(['id' => 'profile-form']); ?>
                <h4>Autorisations</h4>
                <div class="form-group">
                    <label class="control-label">Profil</label>
                    <input type="text" class="form-control" value="<?= $user::getTypeCaption($user->type) ?>" disabled="">
                </div>
                <div class="form-group">
                    <label class="control-label">Début de validité</label>
                    <input type="text" class="form-control" value="<?= $user->createdDate->format('d/m/Y') ?>" disabled="">
                </div>
                <div class="form-group">
                    <label class="control-label">Date de première connexion</label>
                    <input type="text" class="form-control" value="<?= $user->getFirstLoginTime() ?>" disabled="">
                </div>
                <div class="form-group">
                    <label class="control-label">Date de dernière connexion</label>
                    <input type="text" class="form-control" value="<?= $user->getLastLoginTime() ?>" disabled="">
                </div>

                <br>

                <h4>Informations personnelles</h4>
                <?= $form->field($user, 'firstName')->textInput(['disabled' => true]) ?>
                <?= $form->field($user, 'lastName')->textInput(['disabled' => true]) ?>
                <br>
                <?= $form->field($user, 'profession')->dropDownList(\common\models\User::getProfessions(), ['prompt' => '', 'disabled' => Yii::$app->user->identity->type == \common\models\User::TYPE_MANAGER]) ?>
                <?= $form->field($user, 'companyName')->textInput(['maxlength' => 70]) ?>
                <?= $form->field($user, 'activity')->textInput(['maxlength' => 70]) ?>
                <br>
                <?= $form->field($user, 'birthPlace')->textInput(['maxlength' => 150]) ?>
                <?= $form->field($user, 'address')->textInput(['maxlength' => 250]) ?>
                <?= $form->field($user, 'zip')->textInput(['maxlength' => 5]) ?>
                <?= $form->field($user, 'city')->textInput(['maxlength' => 150]) ?>
                <br>
                <?= $form->field($user, 'phone')->textInput(['maxlength' => 10]) ?>
                <?= $form->field($user, 'phoneMobile')->textInput(['maxlength' => 10]) ?>
                <?= $form->field($user, 'email')->textInput(['disabled' => true]) ?>
                <?= $form->field($user, 'isMailingContact')->checkbox() ?>

                <div id="room-filters">
                    <?php foreach ($profiles as $section => $profile): ?>

                        <?php if ($section == DataroomModule::SECTION_COMPANIES): ?>

                            <div class="section-fields section-companies">
                                <h4>Champs à remplir pour recevoir nos actualités AJArepreneurs</h4>
                                <?= $form->field($profiles[$section], 'targetedSector', ['enableClientValidation' => false])->dropDownList($profiles[$section]::sectorList(), ['prompt' => '']) ?>
                                <?= $form->field($profiles[$section], 'targetedTurnover', ['enableClientValidation' => false])->dropDownList($profiles[$section]::turnoverList(), ['prompt' => '']) ?>
                                <?= $form->field($profiles[$section], 'entranceTicket', ['enableClientValidation' => false])->dropDownList($profiles[$section]::ticketList(), ['prompt' => '']) ?>

                                <?= $form->field($profiles[$section], 'geographicalArea', ['enableClientValidation' => false])->dropDownList($profiles[$section]::getGeographicalAreaList(), ['prompt' => '']) ?>
                                <?= $form->field($profiles[$section], 'targetAmount', ['enableClientValidation' => false])->dropDownList($profiles[$section]::getTargetAmountList(), ['prompt' => '']) ?>
                                <?= $form->field($profiles[$section], 'effective', ['enableClientValidation' => false])->textInput() ?>
                            </div>

                        <?php elseif ($section == DataroomModule::SECTION_REAL_ESTATE): ?>

                            <div class="section-fields section-real-estate">
                                <h4>Champs à remplir pour recevoir nos actualités AJAimmo</h4>
                                <?= $form->field($profiles[$section], 'targetSector', ['enableClientValidation' => false])->dropDownList($profiles[$section]::getTargetSectors(), ['prompt' => '']) ?>

                                <?= $form->field($profiles[$section], 'regionIDs', ['enableClientValidation' => false])->widget(\kartik\widgets\Select2::class, [
                                    'data' => ArrayHelper::map(\common\models\Region::find()->all(), 'id', 'nameWithCode'),
                                    'options' => [
                                        'multiple' => true,
                                        'placeholder' => Yii::t('admin', 'Start typing region name'),
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

                                <?= $form->field($profiles[$section], 'targetedAssetsAmount', ['enableClientValidation' => false])->dropDownList($profiles[$section]::getTargetedAssetsAmountList(), ['prompt' => '']) ?>
                                <?= $form->field($profiles[$section], 'assetsDestination', ['enableClientValidation' => false])->dropDownList($profiles[$section]::getAssetsDestinationList(), ['prompt' => '']) ?>
                                <?= $form->field($profiles[$section], 'operationNature', ['enableClientValidation' => false])->dropDownList($profiles[$section]::getOperationNatureList(), ['prompt' => '']) ?>
                            </div>

                        <?php elseif ($section == DataroomModule::SECTION_COOWNERSHIP): ?>

                            <div class="section-fields section-coownership">
                                <h4>Champs à remplir pour recevoir nos actualités AJAsyndic</h4>
                                <?= $form->field($profiles[$section], 'propertyType', ['enableClientValidation' => false])->dropDownList(\backend\modules\dataroom\models\RoomCoownership::getPropertyTypes(), ['prompt' => '']) ?>

                                <?= $form->field($profiles[$section], 'regionIDs', ['enableClientValidation' => false])->widget(\kartik\widgets\Select2::class, [
                                    'data' => ArrayHelper::map(\common\models\Region::find()->all(), 'id', 'nameWithCode'),
                                    'options' => [
                                        'multiple' => true,
                                        'placeholder' => Yii::t('admin', 'Start typing region name'),
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

                                <?= $form->field($profiles[$section], 'lotsNumber', ['enableClientValidation' => false])->textInput() ?>
                                <?= $form->field($profiles[$section], 'coownersNumber', ['enableClientValidation' => false])->textInput() ?>
                            </div>

                        <?php endif ?>
                    <?php endforeach ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Enregistrer les modifications', ['class' => 'btn btn-block submit-btn']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php $this->registerJs("
    $('body').on('change', '#user-ismailingcontact', function(event) {
        if ($(this).prop('checked')) {
            $('#room-filters').show();
        } else {
            $('#room-filters').hide();
        }
    });
    $('#user-ismailingcontact').trigger('change');
") ?>
