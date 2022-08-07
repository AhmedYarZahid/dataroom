<?php

namespace common\rbac;

use Yii;
use yii\rbac\Rule;
use common\models\User;

/**
 * Checks if user type matches
 */
class UserTypeRule extends Rule
{
    public $name = 'userType';

    public function execute($user, $item, $params)
    {
        if ($user) {
            $type = Yii::$app->user->identity->type;

            switch ($item->name) {
                case User::TYPE_SUPERADMIN:
                    return $type == User::TYPE_SUPERADMIN;

                // superadmin can do everything that admin can
                case User::TYPE_ADMIN:
                    return $type == User::TYPE_ADMIN || $type == User::TYPE_SUPERADMIN;

                case User::TYPE_MANAGER:
                    return $type == User::TYPE_MANAGER;

                case User::TYPE_USER:
                    return $type == User::TYPE_USER;
            }
        } else {
            return $item->name == 'anonymous';    
        }

        return false;
    }
}