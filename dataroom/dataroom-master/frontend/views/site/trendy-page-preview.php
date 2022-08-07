<?php

use lateos\trendypage\TrendyPageAsset;

/* @var $this yii\web\View */
/* @var $page \lateos\trendypage\models\TrendyPage */

TrendyPageAsset::register($this);

$this->title = $page->title;
?>

<?= $page->body ?>
