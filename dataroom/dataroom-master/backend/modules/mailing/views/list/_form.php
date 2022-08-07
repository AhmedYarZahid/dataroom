<?php

use backend\assets\MultiSelectAsset;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use backend\modules\mailing\models\MailingContactForm;
use common\helpers\ArrayHelper;

MultiSelectAsset::register($this);

?>

<div class="mailing-list-form">

    <?php $form = ActiveForm::begin([
        'enableClientValidation' => false,
        'validateOnSubmit' => false,
        'layout' => 'horizontal',
    ]); ?>

    <?= $form->field($model, 'name')->textInput() ?>
    
    <div class="pjax-triggers">
        <?= $form->field($contactForm, 'profile')->dropDownList($contactForm->profileList()) ?>

        <div id="filters-block">

            <?= $form->field($contactForm, 'profession')->dropDownList($contactForm->professionList()) ?>

            <?= $form->field($contactForm, 'activity')->dropDownList(MailingContactForm::getActivityList(), ['prompt' => '']) ?>

            <div id="companies-filters-block">
                <fieldset>
                    <legend><?= Yii::t('admin', 'Filters') ?></legend>
                    <?= $form->field($contactForm, 'targetedSector')->dropDownList($contactForm->sectorList()) ?>
                    <?= $form->field($contactForm, 'targetedTurnover')->dropDownList($contactForm->turnoverList()) ?>
                    <?= $form->field($contactForm, 'entranceTicket')->dropDownList($contactForm->ticketList()) ?>

                    <?= $form->field($contactForm, 'geographicalArea')->dropDownList(\backend\modules\dataroom\models\ProfileCompany::getGeographicalAreaList(), ['prompt' => '']) ?>
                    <?= $form->field($contactForm, 'targetAmount')->dropDownList(\backend\modules\dataroom\models\ProfileCompany::getTargetAmountList(), ['prompt' => '']) ?>

                    <div class="form-group field-mailingcontactform-effective">
                        <label class="control-label col-sm-3" for="mailingcontactform-effective">Effectif</label>
                        <div class="col-sm-9">
                            <?= Yii::t('admin', 'between') . '&nbsp;&nbsp;' . Html::activeTextInput($contactForm, 'effectiveMin', ['style' => 'width: 100px;']); ?>
                            &nbsp;
                            <?= Yii::t('admin', 'and') . '&nbsp;&nbsp;' . Html::activeTextInput($contactForm, 'effectiveMax', ['style' => 'width: 100px;']); ?>
                        </div>
                    </div>
                    <hr/>
                </fieldset>
            </div>

            <div id="realestate-filters-block">
                <fieldset>
                    <legend><?= Yii::t('admin', 'Filters') ?></legend>
                    <?= $form->field($contactForm, 'targetSector')->dropDownList(\backend\modules\dataroom\models\ProfileRealEstate::getTargetSectors(), ['prompt' => '']) ?>

                    <?= $form->field($contactForm, 'regionIDs')->widget(\kartik\widgets\Select2::class, [
                        'data' => ArrayHelper::map(\common\models\Region::find()->all(), 'id', 'nameWithCode'),
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

                    <?= $form->field($contactForm, 'targetedAssetsAmount')->dropDownList(\backend\modules\dataroom\models\ProfileRealEstate::getTargetedAssetsAmountList(), ['prompt' => '']) ?>
                    <?= $form->field($contactForm, 'assetsDestination')->dropDownList(\backend\modules\dataroom\models\ProfileRealEstate::getAssetsDestinationList(), ['prompt' => '']) ?>
                    <?= $form->field($contactForm, 'operationNature')->dropDownList(\backend\modules\dataroom\models\ProfileRealEstate::getOperationNatureList(), ['prompt' => '']) ?>
                    <hr/>
                </fieldset>
            </div>

            <div id="coownership-filters-block">
                <fieldset>
                    <legend><?= Yii::t('admin', 'Filters') ?></legend>
                    <?= $form->field($contactForm, 'propertyType')->dropDownList(\backend\modules\dataroom\models\RoomCoownership::getPropertyTypes(), ['prompt' => '']) ?>

                    <?= $form->field($contactForm, 'coownershipRegionIDs')->widget(\kartik\widgets\Select2::class, [
                        'data' => ArrayHelper::map(\common\models\Region::find()->all(), 'id', 'nameWithCode'),
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

                    <?= $form->field($contactForm, 'lotsNumber')->textInput() ?>
                    <?= $form->field($contactForm, 'coownersNumber')->textInput() ?>
                    <hr/>
                </fieldset>
            </div>
        </div>
    </div>
    
    <?php yii\widgets\Pjax::begin(['id' => 'pjax-users']) ?>
        <?= $form->field($model, 'contactIds')->dropDownList($contactForm->contactList(), ['multiple' => true])->label(false) ?>
    <?php Pjax::end(); ?>

    <div class="form-group">
        <div class="col-md-12">
        <?php $label = $model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Update'); ?>
        <?= Html::submitButton($label, ['class' => 'btn btn-primary btn-lg btn-block']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs('
    $("#mailingcontactform-profile").on("change", function(e) {
        var profile = $(this).val();
    
        if (profile == "' . MailingContactForm::PROFILE_SUBSCRIBER . '") {
            $("#mailingcontactform-profession").attr("disabled", false);
            $(".field-mailingcontactform-profession").show();

            $("#mailingcontactform-activity").val("").attr("disabled", true);
            $(".field-mailingcontactform-activity").hide();
        } else if (profile == "' . MailingContactForm::PROFILE_USER . '") {
            $("#mailingcontactform-activity").attr("disabled", false);
            $(".field-mailingcontactform-activity").show();

            $("#mailingcontactform-profession").val("").attr("disabled", true);
            $(".field-mailingcontactform-profession").hide();
        } else {
            $("#filters-block input, #filters-block select").val("").attr("disabled", true);
            $(".field-mailingcontactform-profession, .field-mailingcontactform-activity").hide();
        }

        $("#mailingcontactform-activity").trigger("change");
    });
    $("#mailingcontactform-profile").trigger("change");


    $("#mailingcontactform-activity").on("change", function(e) {
        var activity = $(this).val();

        if (activity == "' . MailingContactForm::ACTIVITY_COMPANIES . '") {
            $("#companies-filters-block input, #companies-filters-block select").attr("disabled", false);

            $("#realestate-filters-block input, #realestate-filters-block select").val("").attr("disabled", true);
            $("#coownership-filters-block input, #coownership-filters-block select").val("").attr("disabled", true);

            $("#mailingcontactform-regionids, #mailingcontactform-coownershipregionids").val("").trigger("change");

            $("#companies-filters-block").show();
            $("#realestate-filters-block, #coownership-filters-block").hide();
        } else if (activity == "' . MailingContactForm::ACTIVITY_REAL_ESTATE . '") {
            $("#realestate-filters-block input, #realestate-filters-block select").attr("disabled", false);

            $("#companies-filters-block input, #companies-filters-block select").val("").attr("disabled", true);
            $("#coownership-filters-block input, #coownership-filters-block select").val("").attr("disabled", true);

            $("#mailingcontactform-coownershipregionids").val("").trigger("change");

            $("#realestate-filters-block").show();
            $("#companies-filters-block, #coownership-filters-block").hide();
        } else if (activity == "' . MailingContactForm::ACTIVITY_COOWNERSHIP . '") {
            $("#coownership-filters-block input, #coownership-filters-block select").attr("disabled", false);

            $("#companies-filters-block input, #companies-filters-block select").val("").attr("disabled", true);
            $("#realestate-filters-block input, #realestate-filters-block select").val("").attr("disabled", true);

            $("#mailingcontactform-regionids").val("").trigger("change");

            $("#coownership-filters-block").show();
            $("#companies-filters-block, #realestate-filters-block").hide();
        } else {
            $("#companies-filters-block input, #companies-filters-block select").val("").attr("disabled", true);
            $("#realestate-filters-block input, #realestate-filters-block select").val("").attr("disabled", true);
            $("#coownership-filters-block input, #coownership-filters-block select").val("").attr("disabled", true);

            $("#mailingcontactform-regionids, #mailingcontactform-coownershipregionids").val("").trigger("change");

            $("#companies-filters-block, #realestate-filters-block, #coownership-filters-block").hide();
        }
    });
    $("#mailingcontactform-activity").trigger("change");


    $(".pjax-triggers select, .pjax-triggers input").on("change", function(e) {
        var form = $(this).parents("form");
        $.pjax.reload("#pjax-users", { type: "POST", data: form.serialize() });
    });

    $(document).on("pjax:end", function(event) {
      initMultiSelect();
    });
    initMultiSelect();

    var multiselectOptions = {
        selectableHeader: "<div class=\"multiselect-header\">Utilisateurs<i class=\"fa fa-angle-double-right select-all\"></i></div>",
        selectionHeader: "<div class=\"multiselect-header\">Utilisateurs ajoutés<i class=\"fa fa-angle-double-left deselect-all\"></i></div>"
    };

    function initMultiSelect() {
        $("#mailinglist-contactids").multiSelect(multiselectOptions);

        $(".select-all").on("click", function(e) {
            $("#mailinglist-contactids").multiSelect("select_all");
        })
        $(".deselect-all").on("click", function(e) {
            $("#mailinglist-contactids").multiSelect("deselect_all");
        })
    }
'); ?>