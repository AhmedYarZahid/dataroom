<?php

namespace common\extensions\arhistory\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\widgets\ActiveForm;
use common\extensions\arhistory\models\ActiveRecordHistory;

/**
 * Comment field for "arhistory" extension
 *
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 * @since 1.0
 */
class ARHistoryCommentField extends Widget
{
    /**
     * @var ActiveForm form instance
     */
    public $form;

    /**
     * @var ActiveRecordHistory model instance
     */
    public $model;

    /**
     * @inheritdoc
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function init()
    {
        if (!$this->form instanceof ActiveForm || !$this->model instanceof ActiveRecordHistory) {
            throw new InvalidConfigException('You should provide "form" and "model" params.');
        }

        if (!$this->model->isNewRecord) {
            echo $this->form->field($this->model, $this->model->historyCommentAttrName)->textarea();
        }

        parent::init();
    }
}