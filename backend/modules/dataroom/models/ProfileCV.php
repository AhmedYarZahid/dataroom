<?php

namespace backend\modules\dataroom\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "ProfileCV".
 *
 * @property integer $userID
 *
 * @property User $user
 */
class ProfileCV extends AbstractProfile
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ProfileCV';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userID'], 'required'],
            [['userID'], 'integer'],
            [['userID'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userID' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userID' => Yii::t('app', 'User ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userID']);
    }
}
