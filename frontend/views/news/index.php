<?php

use yii\helpers\Html;
use frontend\widgets\news\News;
use frontend\widgets\newsletter\Form as NewsletterForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'News');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-index <?= Yii::$app->user->isGuest ? 'is-guest' : '' ?>">

    <div class="newsletter-form">
        <h2 class="newsletter-form-title"><?= Yii::t('app', 'Subscribe to newsletter') ?></h2>
        <?= NewsletterForm::widget() ?>
    </div>

    <div class="news container">
        <?= News::widget([
            'dataProvider' => $dataProvider,
        ]) ?>
    </div>

</div>
