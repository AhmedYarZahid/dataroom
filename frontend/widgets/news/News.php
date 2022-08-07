<?php

namespace frontend\widgets\news;

use backend\modules\news\models\NewsSearch;

/**
 * @author Max Maximov <forlgc@gmail.com>
 */
class News extends \yii\bootstrap\Widget
{
    public $dataProvider;
    public $pageSize = 10;

    public function init()
    {
        parent::init();

        if (!$this->dataProvider) {
            $searchModel = new NewsSearch;
            $this->dataProvider = $searchModel->searchPublishedNews($this->pageSize);
        }
    }

    public function run()
    {
        return $this->render('news', [
            'dataProvider' => $this->dataProvider,
        ]);
    }
}