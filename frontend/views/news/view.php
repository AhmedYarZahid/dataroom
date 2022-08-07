<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use backend\modules\comments\widgets\Comments\Comments;
use backend\modules\comments\models\CommentBundle;
use backend\modules\metatags\models\MetaTags;

/* @var $this yii\web\View */
/* @var $model \backend\modules\news\models\News */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'News'), 'url' => ['/news']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['meta-tags'] = MetaTags::getMetaTagsData(MetaTags::NODE_TYPE_NEWS, $model->id);
?>
<div class="news-index container">

    <p><?= $model->body ?></p>

    <?= Comments::widget([
        'nodeType' => CommentBundle::NODE_TYPE_NEWS,
        'nodeID' => $model->id,
    ]); ?>

</div>
