<?php
namespace backend\modules\contact\models;

use Yii;
use common\models\User;
use backend\modules\contact\interfaces\ContactUserInterface;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @author Perica Levatic <perica.levatic@gmail.com>
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 *
 */
class ContactUser implements ContactUserInterface
{
    /**
     * Get url for user profile (for admin)
     *
     * @author Perica Levatic <perica.levatic@gmail.com>
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param int $userID
     *
     * @return string
     */
    public static function getUserProfileUrl($userID)
    {
        return Url::to(['/user/view', 'id' => $userID]);
    }

    /**
     * Get link to user profile (for admin)
     * if user model is not found by ID, function should try to find user url by email
     *
     * @author Perica Levatic <perica.levatic@gmail.com>
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param int $userID
     * @param string $userEmail
     * @param string $linkText
     *
     * @return string
     */
    public static function getUserProfileLink($userID, $userEmail = null, $linkText = null)
    {
        if (!isset($userID) && isset($userEmail)) {
            $user = User::find()->andWhere(['email' => $userEmail])->one();
        } else {
            $user = User::findOne($userID);
        }

        if (isset($user)) {
            return Html::a(isset($linkText) ? $linkText : $user->email, self::getUserProfileUrl($user->id), array('target' => '_blank'));
        }

        return null;
    }

    /**
     * Check if user with given ID or Email exists in database
     *
     * @author Perica Levatic <perica.levatic@gmail.com>
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param int $userID
     * @param string $userEmail
     * @return boolean
     */
    public static function isUser($userID, $userEmail = null)
    {
        $user = User::findOne($userID);
        if (!isset($userID) && isset($userEmail)) {
            $user = User::find()->andWhere(['email' => $userEmail])->one();
        }

        return isset($user);
    }
}

?>
