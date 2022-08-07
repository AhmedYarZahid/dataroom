<?php

namespace frontend\modules\dataroom\controllers;

class DefaultController extends AbstractController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => '//site/error',
            ],
        ];
    }
}
