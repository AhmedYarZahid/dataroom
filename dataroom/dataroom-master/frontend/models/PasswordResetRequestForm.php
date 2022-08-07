<?php
namespace frontend\models;

use backend\modules\notify\models\Notify;
use common\models\User;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['isActive' => 1],
                'message' => 'There is no user with such email.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'isActive' => 1,
            'email' => $this->email,
        ]);

        if ($user) {
            $user->scenario = 'request-reset-password';

            if (!User::isPasswordResetTokenValid($user->passwordResetToken)) {
                $user->generatePasswordResetToken();
            }

            if ($user->save()) {
                return Notify::sendResetPasswordLink($user);
            }
        }

        return false;
    }
}
