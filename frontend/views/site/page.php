<?php
use yii\helpers\Html;
use backend\modules\comments\widgets\Comments\Comments;
use backend\modules\comments\models\CommentBundle;
use backend\modules\metatags\models\MetaTags;

/* @var $this yii\web\View */
/* @var $page \backend\modules\staticpage\models\StaticPage */

$this->title = $page->title;
$this->params['breadcrumbs'][] = $this->title;

$this->params['meta-tags'] = MetaTags::getMetaTagsData(MetaTags::NODE_TYPE_STATICPAGE, $page->id);
?>

<div class="site-about container">
    <p><?= $page->body ?></p>

    <?= Comments::widget([
        'nodeType' => CommentBundle::NODE_TYPE_STATICPAGE,
        'nodeID' => $page->id,
    ]); ?>
</div>
