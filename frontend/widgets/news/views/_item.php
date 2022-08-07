<?php

use kartik\helpers\Html;
use common\helpers\DateHelper;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $model backend\modules\news\models\News */

setlocale(LC_TIME, 'fr_FR.UTF-8');
?>

<div class="news-item-image col-md-3">
    <?php if ($model->image): ?>
        <?= Html::img($model->getImageUrl()) ?>
    <?php endif; ?>
</div>
<div class="news-item-content col-md-9">
    <div class="news-item-content-title"><?= $model->title ?></div>
    <div class="news-item-content-date">
        <?= strftime('%e %B %G', strtotime($model->publishDate)) ?>        
    </div>
    <div class="news-item-content-body"><?= StringHelper::truncateWords(strip_tags($model->body), 20) ?></div>
    <?= Html::a(Yii::t('app', 'Read more'), ['/news/view', 'id' => $model->id], ['class' => 'news-item-content-read-more']) ?>
</div>
