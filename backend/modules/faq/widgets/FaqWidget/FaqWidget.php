<?php

namespace backend\modules\faq\widgets\FaqWidget;

use Yii;
use yii\bootstrap\Widget;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use backend\modules\faq\models\FaqCategory;
use backend\modules\faq\models\FaqCategorySearch;
use backend\modules\faq\models\FaqItem;
use backend\modules\faq\models\FaqItemSearch;


class FaqWidget extends Widget
{
    /**
     * @var bool|int if id defined then this FAQ will be opened
     */
    public $id = false;
    /**
     * @var bool|string title for FAQ page
     */
    public $title = false;
    /**
     * @var bool|string breadcrumbs for FAQ page
     */
    public $breadcrumbs = false;
    /**
     * @var string path to view
     */
    public $viewPath = 'index';

    /**
     * @return string
     */
    public function run()
    {
        $data = [];
        $categories = FaqCategory::getList();

        foreach($categories as $category) {
            $data[] = [
                'category' => $category,
                'items' => FaqItem::getList($category->id),
            ];
        }

        return $this->render($this->viewPath, [
            'data' => $data,
            'id' => $this->id,
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
        ]);
    }
}