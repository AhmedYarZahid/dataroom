<?php

namespace common\helpers;

use yii\helpers\Html;

class FormHelper
{
    public static function downloadLink($model, $attribute)
    {
        if ($model->hasMethod('getDocumentUrl') && $model->getDocumentUrl($attribute)) {
            return Html::a('Télécharger un fichier', $model->getDocumentUrl($attribute), ['target' => '_blank']);
        }
        
        return false;
    }
}