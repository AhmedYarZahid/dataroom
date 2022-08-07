<?php

use yii\helpers\Html;
use yii\widgets\ListView;

?>

<div class="news-index container">
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'options' => [
            'class' => 'news-index-list',
        ],
        'itemOptions' => ['class' => 'news-item row'],
        'itemView' => '_item',
        'summary' => '',
        'pager' => [
            'prevPageLabel' => Yii::t('app', 'Previous'),
            'nextPageLabel' => Yii::t('app', 'Next'),
        ],
    ]) ?>
</div>
