<?php
namespace frontend\controllers;

use common\models\JobOffer;
use common\models\JobOfferSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use frontend\controllers\Controller as FrontendController;

/**
 * Job Offer controller
 */
class JobOfferController extends FrontendController
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
     * Job Offers list
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new JobOfferSearch();
        $dataProvider = $searchModel->searchPublished(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * View job offer by specified id
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param int $id
     * @return string
     * @throws HttpException
     */
    public function actionView($id)
    {
        $model = JobOffer::find()
            ->removed(false)
            ->published()
            ->andWhere(['id' => $id])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException;
        }

        return $this->render('view', [
            'model' => $model
        ]);
    }
}
