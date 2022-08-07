<?php

namespace frontend\widgets\cookiebar;

use Yii;
use yii\base\Widget;
use \yii\helpers\Json;

/**
 * Jquery Cookie Bar
 *
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 */
class CookieBar extends Widget
{
    // Plugin Options
    public $fixed = true;

    public $acceptOnScroll = false;

    public $acceptText = "J'accepte";

    public $policyButton = false;

    public $policyText = 'Privacy Policy';

    public $policyURL = '/privacy-policy/';

    public $message = 'Ce site utilise des cookies pour améliorer votre expérience de navigation.';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        CookieBarAsset::register(Yii::$app->view);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        Yii::$app->getView()->registerJs("
            $.cookieBar({
                fixed: " . Json::encode($this->fixed) . ",
                acceptOnScroll: " . Json::encode($this->acceptOnScroll) . ",
                acceptText: " . Json::encode($this->acceptText) . ",
                policyButton: " . Json::encode($this->policyButton) . ",
                policyText: " . Json::encode($this->policyText) . ",
                policyURL: " . Json::encode($this->policyURL) . ",
                message: " . Json::encode($this->message) . "
            });

            $('#cookie-bar').append('<span class=\"close-icon\">X</span>');
            $(document).on('click','.close-icon', function() {
                jQuery('#cookie-bar').hide();
            });

            //menu toggle
            $('.nav-button').on('click', function() {
                $(this).parents('#header').find('nav.menu_container').toggleClass('open-nav');
                $(this).toggleClass('active');
            });

            $('.menu-item-has-children').on('click', function() {
                $(this).find('.sub_menu').toggleClass('open');
                $(this).toggleClass('active');
            });
        ");
    }
}
