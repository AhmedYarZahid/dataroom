<?php

namespace frontend\widgets\rooms;

use backend\modules\dataroom\models\search\RoomCompanySearch;

/**
 * @author Max Maximov <forlgc@gmail.com>
 */
class Rooms extends \yii\bootstrap\Widget
{
    public function run()
    {
        $searchModel = new RoomCompanySearch;
        $models = $searchModel->getLatestOffers();

        return $this->render('rooms', [
            'models' => $models,
        ]);
    }
}