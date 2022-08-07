<?php

namespace backend\modules\comments\widgets\CommentAdmin;

use Yii;
use yii\bootstrap\Widget;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use backend\modules\comments\models\CommentBundle;


class CommentAdmin extends Widget
{
    /**
     * @var string Type of node to apply comments to
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
        $model = CommentBundle::find()->where([
            'nodeType' => $this->nodeType,
            'nodeID' => $this->nodeID
        ])->one();

        if (!$model) {
            $model = new CommentBundle();
            $model->loadDefaultValues();
            $model->nodeType = $this->nodeType;
            $model->nodeID = $this->nodeID;
        }

        return $this->render('index', [
            'commentModel' => $model,
            'form' => $this->form,
        ]);
    }
}