<?php

namespace backend\modules\comments\models;

use yii\db\ActiveQuery;

class CommentQuery extends ActiveQuery
{
    public function approved($approved = true)
    {
        $this->andWhere(['isApproved' => intval($approved)]);

        return $this;
    }
}