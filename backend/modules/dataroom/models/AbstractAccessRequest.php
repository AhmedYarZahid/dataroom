<?php

namespace backend\modules\dataroom\models;

abstract class AbstractAccessRequest extends \yii\db\ActiveRecord
{
    abstract function getUrl();
    
    public static function primaryKey()
    {
        return ['accessRequestID'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccessRequest()
    {
        return $this->hasOne(RoomAccessRequest::className(), ['id' => 'accessRequestID']);
    }
}