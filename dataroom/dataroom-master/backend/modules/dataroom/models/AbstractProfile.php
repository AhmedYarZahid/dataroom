<?php

namespace backend\modules\dataroom\models;

use Yii;
use common\models\User;

abstract class AbstractProfile extends \yii\db\ActiveRecord
{
    public static function primaryKey()
    {
        return ['userID'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userID']);
    }
}