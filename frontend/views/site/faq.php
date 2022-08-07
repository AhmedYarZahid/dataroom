<?php
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'FAQ';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-faq container">
    <?= backend\modules\faq\widgets\FaqWidget\FaqWidget::widget(); ?>
</div>
