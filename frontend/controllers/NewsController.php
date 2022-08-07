<?php
namespace frontend\controllers;

use Yii;
use backend\modules\news\models\News;
use backend\modules\news\models\NewsSearch;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use frontend\controllers\Controller as FrontendController;

/**
 * News controller
 */
class NewsController extends FrontendController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * News list
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->searchPublishedNews();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCategory($category)
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->searchInCategory($category);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * View news by specified id
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = News::getNews($id);

        if (!$model) {
            throw new NotFoundHttpException;
        }

        return $this->render('view', [
            'model' => $model
        ]);
    }
}
