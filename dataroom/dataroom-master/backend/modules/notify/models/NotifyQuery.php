<?php

namespace backend\modules\notify\models;

use omgdef\multilingual\MultilingualTrait;
use yii\db\ActiveQuery;

class NotifyQuery extends ActiveQuery
{
    use MultilingualTrait;

    public function event($event)
    {
        $this->andWhere(['eventID' => intval($event)]);

        return $this;
    }

    public function defaultTemplate($state = true)
    {
        $this->andWhere(['isDefault' => $state]);

        return $this;
    }
}