<?php

use backend\assets\MultiSelectAsset;
use backend\modules\contact\models\Contact;
use backend\modules\dataroom\models\ProfileCompany;
use backend\modules\dataroom\models\ProfileRealEstate;
use backend\modules\dataroom\models\RoomCoownership;
use backend\modules\mailing\controllers\ListController;
use common\models\Region;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\JsExpression;
use yii\widgets\Pjax;
use backend\modules\mailing\models\MailingContactForm;
use common\helpers\ArrayHelper;

MultiSelectAsset::register($this);


$this->registerCssFile("https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css", [
    'depends' => backend\assets\AppAsset::class
]);
$this->registerJsFile("https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js", [
    'depends' => backend\assets\AppAsset::class

]);
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
                <div id="change-profession-link">
                    <label class="control-label col-sm-3" for="mailinglist-name"></label>
                    <div class="form-group field-mailinglist-name required">
                        <div class="col-sm-6">
                            <a target="_blank" href="<?= \yii\helpers\Url::to(['/profession']) ?>">Changer de métier</a>
                        </div>
                    </div>
                </div>

                <?= $form->field($contactForm, 'activity')->dropDownList(MailingContactForm::getActivityList(), ['prompt' => '']) ?>

                <div id="companies-filters-block">
                    <fieldset>
                        <legend><?= Yii::t('admin', 'Filters') ?></legend>
                        <?php
                        $targetedSector = $contactForm->sectorList();
                        unset($targetedSector[""]);
                        ?>
                        <?= $form->field($contactForm, 'targetedSector')->widget(Select2::class, [
                            'data' => $targetedSector,
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '',
                                'options' => ListController::makeSelections(explode(",", $filters->targetedSector))
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
                        <?php
                        $targetedTurnover = $contactForm->turnoverList();
                        unset($targetedTurnover[""]);
                        ?>
                        <?= $form->field($contactForm, 'targetedTurnover')->widget(Select2::class, [
                            'data' => $targetedTurnover,
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '',
                                'options' => ListController::makeSelections(explode(",", $filters->targetedTurnover))
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
                        <?php
                        $entranceTicket = $contactForm->ticketList();
                        unset($entranceTicket[""]);
                        ?>
                        <?= $form->field($contactForm, 'entranceTicket')->widget(Select2::class, [
                            'data' => $entranceTicket,
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '',
                                'options' => ListController::makeSelections(explode(",", $filters->entranceTicket))
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
                        <?= $form->field($contactForm, 'geographicalArea')->widget(Select2::class, [
                            'data' => ProfileCompany::getGeographicalAreaList(),
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '',
                                'options' => ListController::makeSelections(explode(",", $filters->geographicalArea))
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
                        <?= $form->field($contactForm, 'targetAmount')->widget(Select2::class, [
                            'data' => ProfileCompany::getTargetAmountList(),
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '',
                                'options' => ListController::makeSelections(explode(",", $filters->targetAmount))
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
                        <?= $form->field($contactForm, 'targetSector')->widget(Select2::class, [
                            'data' => ProfileRealEstate::getTargetSectors(),
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '',
                                'options' => ListController::makeSelections(explode(",", $filters->targetSector))
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'tags' => false,
                                'language' => [
                                    'noResults' => new JsExpression('function() {
                                                return "' . Yii::t('admin', 'No target sector found.') . '";
                                            }'),
                                ],
                            ],
                            'pluginEvents' => [],
                        ]); ?>
                        <?= $form->field($contactForm, 'regionIDs')->widget(Select2::class, [
                            'data' => ArrayHelper::map(Region::find()->all(), 'id', 'nameWithCode'),
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '',
                                'options' => ListController::makeSelections(explode(",", $filters->regionIDs))
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
                        <?= $form->field($contactForm, 'targetedAssetsAmount')->widget(Select2::class, [
                            'data' => ProfileRealEstate::getTargetedAssetsAmountList(),
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '',
                                'options' => ListController::makeSelections(explode(",", $filters->targetedAssetsAmount))
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'tags' => false,
                                'language' => [
                                    'noResults' => new JsExpression('function() {
                                                return "' . Yii::t('admin', 'No targeted assets amount found.') . '";
                                            }'),
                                ],
                            ],
                            'pluginEvents' => [],
                        ]); ?>
                        <?= $form->field($contactForm, 'assetsDestination')->widget(Select2::class, [
                            'data' => ProfileRealEstate::getAssetsDestinationList(),
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '',
                                'options' => ListController::makeSelections(explode(",", $filters->assetsDestination))
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'tags' => false,
                                'language' => [
                                    'noResults' => new JsExpression('function() {
                                                return "' . Yii::t('admin', 'No assets destination found.') . '";
                                            }'),
                                ],
                            ],
                            'pluginEvents' => [],
                        ]); ?>
                        <?= $form->field($contactForm, 'operationNature')->widget(Select2::class, [
                            'data' => ProfileRealEstate::getOperationNatureList(),
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '',
                                'options' => ListController::makeSelections(explode(",", $filters->operationNature))
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'tags' => false,
                                'language' => [
                                    'noResults' => new JsExpression('function() {
                                                return "' . Yii::t('admin', 'No operation nature found.') . '";
                                            }'),
                                ],
                            ],
                            'pluginEvents' => [],
                        ]); ?>
                        <hr/>
                    </fieldset>
                </div>

                <div id="coownership-filters-block">
                    <fieldset>
                        <legend><?= Yii::t('admin', 'Filters') ?></legend>
                        <?= $form->field($contactForm, 'propertyType')->widget(Select2::class, [
                            'data' => RoomCoownership::getPropertyTypes(),
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '',
                                'options' => ListController::makeSelections(explode(",", $filters->propertyType))
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'tags' => false,
                                'language' => [
                                    'noResults' => new JsExpression('function() {
                                                return "' . Yii::t('admin', 'No property type found.') . '";
                                            }'),
                                ],
                            ],
                            'pluginEvents' => [],
                        ]); ?>
                        <?= $form->field($contactForm, 'coownershipRegionIDs')->widget(Select2::class, [
                            'data' => ArrayHelper::map(Region::find()->all(), 'id', 'nameWithCode'),
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '',
                                'options' => ListController::makeSelections(explode(",", $filters->coownershipRegionIDs))
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

        <div class="form-group field-extramailinglist-name required">
            <label class="control-label col-sm-3" for="mailinglist-name">Extra contact IDs</label>
            <div class="col-sm-6">
                <select  name="extraContacts[]" class="col-md-12" multiple="multiple" id="extra-contact-tags">
                    <?php
                    $contacts = ArrayHelper::map(Contact::find()->all(), 'id', 'email');
                    $extraContacts = explode(",", $filters->extraContacts);
                    foreach ($contacts as $contact){
                        $selected = in_array($contact, $extraContacts) ? "selected" : "";
                        echo "<option value='$contact' $selected>$contact</option>";
                    } ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12">
                <?php $label = $model->isNewRecord ? Yii::t('admin', 'Create') : Yii::t('admin', 'Update'); ?>
                <?= Html::submitButton($label, ['class' => 'btn btn-primary btn-lg btn-block']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>


<?php $this->registerJs('


        $("#extra-contact-tags").select2({
        tags: true
        });
        
$("#mailinglist-extracontactids").multiSelect()
       $("#add-extra-email").on("click",function(){
       let extra_email = $("#extra-email").val();
       
          if(extra_email.length >0){
          $("#mailinglist-extracontactids").multiSelect("addOption", { value: extra_email, text: extra_email, index: 0, nested: "optgroup_label" });
          }
    $("#extra-email").val("");
       })


    $("#mailingcontactform-profile").on("change", function(e) {
        var profile = $(this).val();
    
        if (profile == "' . MailingContactForm::PROFILE_SUBSCRIBER . '") {
            $("#mailingcontactform-profession").attr("disabled", false);
            $(".field-mailingcontactform-profession").show();
            $("#change-profession-link").show();

            $("#mailingcontactform-activity").val("").attr("disabled", true);
            $(".field-mailingcontactform-activity").hide();
        } else if (profile == "' . MailingContactForm::PROFILE_USER . '") {
            $("#mailingcontactform-activity").attr("disabled", false);
            $(".field-mailingcontactform-activity").show();

            $("#mailingcontactform-profession").val("").attr("disabled", true);
            $(".field-mailingcontactform-profession").hide();
                        $("#change-profession-link").hide();
' .
    '
        } else {
            $("#filters-block input, #filters-block select").val("").attr("disabled", true);
            $(".field-mailingcontactform-profession, .field-mailingcontactform-activity").hide();
                        $("#change-profession-link").hide();

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