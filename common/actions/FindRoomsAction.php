<?php

namespace common\actions;

use backend\modules\dataroom\models\Room;
use Yii;
use yii\base\Action;
use yii\web\Response;

class FindRoomsAction extends Action
{
    /**
     * @inheritdoc
     */
    public function init()
    {

    }

    /**
     * @inheritdoc
     *
     * Find rooms by term. Approach for Select2.
     */
    public function run($q)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = Room::find();

        $query->andWhere(['like', 'title', $q])
            ->limit(50)
            ->orderBy('title ASC');

        $roomsList = $query->all();

        $out = ['results' => []];
        foreach ($roomsList as $room) {
            $out['results'][] = [
                'id' => $room->id,
                'text' => $room->title,
            ];
        }

        return $out;
    }
}