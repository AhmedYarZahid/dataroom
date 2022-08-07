<?php

namespace backend\modules\comments\models;

use yii\db\ActiveQuery;

class CommentBundleQuery extends ActiveQuery
{
    public function active($active = true)
    {
        $this->andWhere(['isActive' => intval($active)]);

        return $this;
    }

    public function newCommentsAllowed($isNewCommentsAllowed = true)
    {
        $this->andWhere(['isNewCommentsAllowed' => intval($isNewCommentsAllowed)]);

        return $this;
    }
}