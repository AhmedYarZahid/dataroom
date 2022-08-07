<?php

namespace backend\modules\metatags\widgets\MetaTags;

use Yii;
use yii\bootstrap\Widget;
use yii\helpers\Html;

class MetaTags extends Widget
{
    /**
     *
     * @var array Array of meta tags data
     */
    public $data;
    
    /**
     * @return mixed string|null
     */
    public function run()
    {
        $result = '';

        if (empty($this->data) || !is_array($this->data)) {
            return $result;
        }

        foreach ($this->data as $metaTag) {
            if (is_array($metaTag) && !empty($metaTag['attrName']) && !empty($metaTag['attrValue']) && !empty($metaTag['content'])) {
                $attrName = Html::encode($metaTag['attrName']);
                $attrValue = Html::encode($metaTag['attrValue']);
                $content = Html::encode($metaTag['content']);

                $result .= Html::tag('meta', '', [$attrName => $attrValue, 'content' => $content]) . "\n\t";
            }
        }

        return $result;
    }
}