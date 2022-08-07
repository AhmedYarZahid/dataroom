<?php

namespace backend\modules\faq\models;

use yii\db\ActiveQuery;
use omgdef\multilingual\MultilingualTrait;

class FaqCategoryQuery extends ActiveQuery
{
    use MultilingualTrait;
}