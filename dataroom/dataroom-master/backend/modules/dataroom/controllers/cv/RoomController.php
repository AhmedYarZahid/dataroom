<?php

namespace backend\modules\dataroom\controllers\cv;

use common\actions\GetCvFunctionChildsAction;
use common\actions\GetRegionByDepartmentAction;
use Yii;
use backend\modules\dataroom\controllers\AbstractRoomController;
use backend\modules\dataroom\models\RoomCV;
use backend\modules\dataroom\models\search\RoomCVSearch;
use yii\web\BadRequestHttpException;

/**
 * RoomController implements the CRUD actions for RoomCV model.
 */
class RoomController extends AbstractRoomController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'AJAreclassement');
        $this->titleSmall = Yii::t('admin', 'Manage CV offers');

        $this->modelClass = RoomCV::class;
        $this->searchModelClass = RoomCVSearch::class;
        $this->proposalSearchModelClass = null;

        parent::init();
    }

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

        $this->redirect('index');
    }
}
