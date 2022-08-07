<?php

namespace backend\components\gridview;

use kartik\grid\CheckboxColumn as KartikCheckboxColumn;
use yii\helpers\Html;

/**
 * The AltCheckboxColumn resolves a problem using CheckboxColumn together with iCheck in AdminLTE plugin.
 *
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 */
class AltCheckboxColumn extends KartikCheckboxColumn
{
    protected function renderHeaderCellContent() {
        return Html::checkbox($this->getHeaderCheckBoxName(), false, ['class' => 'select-on-check-all simple']);
    }
}
