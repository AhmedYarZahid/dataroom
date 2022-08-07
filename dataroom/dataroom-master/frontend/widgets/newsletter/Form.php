<?php

namespace frontend\widgets\newsletter;

use Yii;
use common\models\Newsletter;

/**
 * @author Max Maximov <forlgc@gmail.com>
 */
class Form extends \yii\bootstrap\Widget
{
    public $newsletterModel;
    public $submitted;

    public function init()
    {
        parent::init();

        if (!$this->newsletterModel) {
            $this->newsletterModel = new Newsletter;
            $this->newsletterModel->scenario = Newsletter::SCENARIO_NEWSLETTER_FORM;
        }
    }

    public function run()
    {
        return $this->render('form', [
            'model' => $this->newsletterModel,
            'submitted' => $this->submitted,
        ]);
    }
}