<?php
use yii\helpers\Html;
use lateos\formpage\FormPageAsset;
use lateos\formpage\widgets\FormPage\FormPage;
use backend\modules\metatags\models\MetaTags;

/* @var $this yii\web\View */
/* @var $page lateos\formpage\models\FormPage */
/* @var $formModel lateos\formpage\models\FormPageDynamicModel */

FormPageAsset::register($this);

$this->title = $page->title;
$this->params['breadcrumbs'][] = $page->title;

$this->params['meta-tags'] = MetaTags::getMetaTagsData(MetaTags::NODE_TYPE_FORMPAGE, $page->id);

?>

<div class="container">
    <?= FormPage::widget([
        'page' => $page,
        'formModel' => $formModel,
    ]); ?>
</div>
