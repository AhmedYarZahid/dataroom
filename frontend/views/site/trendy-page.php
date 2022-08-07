<?php
use yii\helpers\Html;
use lateos\trendypage\TrendyPageAsset;
use frontend\assets\TrendyPageProjectStylesAsset;
use backend\modules\comments\widgets\Comments\Comments;
use backend\modules\comments\models\CommentBundle;
use backend\modules\metatags\models\MetaTags;
use frontend\widgets\news\News;
use frontend\widgets\newsletter\Form as NewsletterForm;
use frontend\widgets\rooms\Rooms;

/* @var $this yii\web\View */
/* @var $page \lateos\trendypage\models\TrendyPage */

TrendyPageAsset::register($this);
TrendyPageProjectStylesAsset::register($this);

$this->title = $page->title;

$this->params['homepage'] = $page->id == 1;

if (!$this->params['homepage']) {
    $this->params['breadcrumbs'][] = $page->title;    
}

$this->params['meta-tags'] = MetaTags::getMetaTagsData(MetaTags::NODE_TYPE_TRENDYPAGE, $page->id);

// Apply dynamic blocks
/*$newsBlockTag = '<dynamic-block-news></dynamic-block-news>';
$newsletterBlockTag = '<dynamic-block-newsletter></dynamic-block-newsletter>';
if (strpos($page->body, $newsBlockTag) !== false) {
    $page->body = str_replace($newsBlockTag, News::widget(), $page->body);
}
if (strpos($page->body, $newsletterBlockTag) !== false) {
    $page->body = str_replace($newsletterBlockTag, NewsletterForm::widget(), $page->body);
}*/

$offersTag = '<dynamic-block-offers></dynamic-block-offers>';
if (strpos($page->body, $offersTag) !== false) {
    $page->body = str_replace($offersTag, Rooms::widget(), $page->body);
}
?>

<?= $page->body ?>

<div class="container">
    <?= Comments::widget([
        'nodeType' => CommentBundle::NODE_TYPE_TRENDYPAGE,
        'nodeID' => $page->id,
]); ?>
</div>
