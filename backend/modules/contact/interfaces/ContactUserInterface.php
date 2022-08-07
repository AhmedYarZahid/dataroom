<?php

namespace backend\modules\contact\interfaces;

/**
 * @author Perica Levatic <perica.levatic@gmail.com>
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 */

interface ContactUserInterface
{
    /**
     * Get url for user profile (for admin)
     * 
     * @author Perica Levatic <perica.levatic@gmail.com>
     *
     * @param string $userID
     * @return  string
     */
    public static function getUserProfileUrl($userID);


    /**
     * Get link to user profile (for admin)
     * if user model is not found by ID, function should try to find user url by email
     * 
     * @author Perica Levatic <perica.levatic@gmail.com>
     *
     * @param string $userID
     * @param string $userEmail
     * @param string $linkText
     * @return  string
     */
    public static function getUserProfileLink($userID, $userEmail = null, $linkText = null);


    /**
     * Check if user with given ID or Email exists in database
     * 
     * @author Perica Levatic <perica.levatic@gmail.com>
     *
     * @param string $userID
     * @param string $userEmail
     * @return  boolean
     */
    public static function isUser($userID, $userEmail = null);
}