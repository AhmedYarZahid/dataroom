<?php

namespace backend\modules\news\models;

use omgdef\multilingual\MultilingualTrait;
use yii\db\ActiveQuery;

class NewsQuery extends ActiveQuery
{
    use MultilingualTrait;

    public function active($active = true)
    {
        $this->andWhere(['isActive' => intval($active)]);

        return $this;
    }

    public function published($published = true)
    {
        if ($published) {
            $this->andWhere('isActive = 1 AND publishDate IS NOT NULL AND publishDate <= CURDATE()');
        } else {
            $this->andWhere('isActive = 0 OR publishDate IS NULL OR publishDate > CURDATE()');
        }

        return $this;
    }
}