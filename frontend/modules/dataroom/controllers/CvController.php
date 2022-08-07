<?php

namespace frontend\modules\dataroom\controllers;

use backend\modules\dataroom\models\RoomAccessRequestCV;
use backend\modules\dataroom\models\RoomCV;
use backend\modules\dataroom\models\search\RoomCVSearch;
use common\actions\GetCvFunctionChildsAction;
use common\actions\GetRegionByDepartmentAction;
use yii\web\BadRequestHttpException;

class CvController extends AbstractRoomController
{
    protected $modelClass = RoomCV::class;
    protected $searchModelClass = RoomCVSearch::class;
    protected $accessRequestClass = RoomAccessRequestCV::class;
    protected $proposalClass = null;

    protected $proposalFileName = null;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'get-cv-function-childs' => [
                'class' => GetCvFunctionChildsAction::class,
            ],
            'get-region-by-department' => [
                'class' => GetRegionByDepartmentAction::class,
            ]
        ];
    }

    /**
     * Deactivate CV Room
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param int $id
     * @return bool
     * @throws BadRequestHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDeactivate($id)
    {
        $detailedRoomModel = $this->findModel($id);
        if ($detailedRoomModel->state != RoomCV::STATE_READY) {
            throw new BadRequestHttpException();
        }

        $this->roomManager->deactivateCVRoom($detailedRoomModel);

        \Yii::$app->getSession()->setFlash('success', \Yii::t('app', 'Room has been deactivated successfully.'));

        $this->redirect(['update-room', 'id' => $detailedRoomModel->id]);
    }
}