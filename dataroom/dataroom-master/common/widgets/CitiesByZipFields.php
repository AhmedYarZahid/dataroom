<?php

namespace common\widgets;

use kartik\helpers\Html;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * Fields to fill cities by zip
 *
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 * @since 1.0
 */
class CitiesByZipFields extends Widget
{
    /**
     * @var ActiveForm form instance
     */
    public $form;

    /**
     * @var ActiveRecord Model instance
     */
    public $model;

    /**
     * @var bool whether to use wrappers for each field
     */
    public $useWrappers = false;

    /**
     * @var string wrapper for zip field
     */
    public $zipWrapper = '<div class="col-sm-2">{FIELD}</div>';

    /**
     * @var string wrapper for city field
     */
    public $cityWrapper = '<div class="col-sm-4">{FIELD}</div>';

    /**
     * @var string used to render only specified field
     */
    public $attribute = null;

    /**
     * @inheritdoc
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function init()
    {
        if (!$this->form instanceof ActiveForm || !$this->model instanceof ActiveRecord) {
            throw new InvalidConfigException('You should provide "form" and "model" params.');
        }

        if ($this->attribute === null || $this->attribute == 'zip') {
            $zipField = $this->form->field($this->model, 'zip')->textInput(['maxlength' => 5])->hint(Yii::t('app', 'Please provide zip in order to choose city.'));
            if ($this->useWrappers) {
                $zipField = str_replace('{FIELD}', $zipField, $this->zipWrapper);
            }
            echo $zipField;
        }

        if ($this->attribute === null || $this->attribute == 'cityID') {
            $cityField = $this->form->field($this->model, 'cityID')->dropDownList([], ['prompt' => Yii::t('app', '- City -')]);
            if ($this->useWrappers) {
                $cityField = str_replace('{FIELD}', $cityField, $this->cityWrapper);
            }
            echo $cityField;
        }

        Yii::$app->getView()->registerJs('
            $("body").on("keyup blur", "#' . Html::getInputId($this->model, 'zip') . '", function() {
                if ($(this).val().length >= 4) {
                    $.get( "' . Url::to(['/site/get-cities-by-zip']) . '", {zip: $(this).val(), selected: "' . $this->model->cityID . '"})
                        .success(function(data) {
                            if (!data) {
                                $( "#'.Html::getInputId($this->model, 'cityID').'" ).html("<option value=\'\'>' . Yii::t('app', '- City -') . '</option>");
                            } else {
                                $( "#'.Html::getInputId($this->model, 'cityID').'" ).html(data);
                            }
                        }
                    );
                } else {
                    $( "#'.Html::getInputId($this->model, 'cityID').'" ).html("<option value=\'\'>' . Yii::t('app', '- City -') . '</option>");
                }
            });

            $("#' . Html::getInputId($this->model, 'zip') . '").trigger("blur");
        ');

        parent::init();
    }
}