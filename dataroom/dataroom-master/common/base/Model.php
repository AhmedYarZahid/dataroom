<?php

namespace common\base;

use Yii;
use yii\helpers\ArrayHelper;

class Model extends \yii\base\Model
{
    /**
     * Creates and populates a set of models.
     *
     * @param string $modelClass
     * @param array $multipleModels
     * @param array $defaultData
     * @return array
     */
    public static function createMultiple($modelClass, $multipleModels = [], $defaultData = [])
    {
        $model    = new $modelClass;
        $formName = $model->formName();
        $post     = Yii::$app->request->post($formName);
        $models   = [];
        
        if (!empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (!is_numeric($i)) {
                    continue;
                }

                if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                    $createdModel = $multipleModels[$item['id']];
                    $createdModel->attributes = $defaultData;

                    $models[] = $createdModel;
                } else {
                    $createdModel = new $modelClass;
                    $createdModel->attributes = $defaultData;

                    $models[] = $createdModel;
                }
            }
        }

        unset($model, $formName, $post);

        return $models;
    }

    /**
     * @inheritdoc
     */
    public static function validateMultiple($models, $attributeNames = null, $uniqueAttributes = [])
    {
        if (($valid = parent::validateMultiple($models, $attributeNames)) && !empty($uniqueAttributes)) {
            foreach ($uniqueAttributes as $uniqueAttribute) {
                $valuesList = [];

                /* @var $model Model */
                foreach ($models as $model) {
                    if (in_array($model->$uniqueAttribute, $valuesList)) {
                        $model->addError($uniqueAttribute, Yii::t('app', 'Please remove duplicated {field}', ['field' => $model->getAttributeLabel($uniqueAttribute)]));
                        $valid = false;
                    }
                    else {
                        $valuesList[] = $model->$uniqueAttribute;
                    }
                }
            }
        }

        return $valid;
    }

    /**
     * Get errors for multiple models
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param Model[] $models
     * @param string $attribute
     * @return array
     */
    public static function getErrorsMultiple($models, $attribute = null)
    {
        $result = [];
        foreach ($models as $model) {
            if ($errors = $model->getErrors($attribute)) {
                $result[] = $errors;
            }
        }

        return $result;
    }
}