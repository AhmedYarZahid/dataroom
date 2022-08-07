<?php

namespace backend\modules\metatags\widgets\MetaTagsAdmin;

use Yii;
use yii\bootstrap\Widget;
use backend\modules\metaTags\models\MetaTags;


class MetaTagsAdmin extends Widget
{
    /**
     * @var string Type of node to apply metatags to
     */
    public $nodeType;

    /**
     * @var int Node ID
     */
    public $nodeID;

    /**
     * @var ActiveForm Form object
     */
    public $form;

    /**
     * @return string
     */
    public function run()
    {
        $model = MetaTags::find()->where([
            'nodeType' => $this->nodeType,
            'nodeID' => $this->nodeID
        ])->one();

        if (!$model) {
            $model = new MetaTags();
            $model->loadDefaultValues();
            $model->nodeType = $this->nodeType;
            $model->nodeID = $this->nodeID;
        }

        return $this->render('index', [
            'metaTagsModel' => $model,
            'form' => $this->form,
        ]);
    }
}